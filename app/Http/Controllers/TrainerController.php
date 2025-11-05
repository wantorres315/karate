<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Club;
use Illuminate\Http\Request;
use App\Role;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $query = Profile::with(['user','club','trainingClubs'])->where('is_treinador', true);

        // Clubs para filtro
        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            $clubs = Club::orderBy('name')->get(['id', 'name']);
            if ($request->filled('club_id')) {
                $query->where('club_id', $request->integer('club_id'));
            }
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            $trainerClubId = optional($user->profiles[0] ?? null)->club_id;
            $query->where('club_id', $trainerClubId);
            $clubs = Club::where('id', $trainerClubId)->get(['id', 'name']);
        } else {
            abort(403);
        }

        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('number_kak', 'like', "%{$term}%")
                  ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$term}%"))
                  ->orWhereHas('club', fn($c) => $c->where('name', 'like', "%{$term}%"));
            });
        }

        $trainers = $query->orderBy('name')->paginate($perPage)->withQueryString();
        return view('trainers.index', compact('trainers', 'clubs'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            $clubs = Club::orderBy('name')->get(['id', 'name']);
            $members = Profile::with('club')
                ->where('is_treinador', false)
                ->orderBy('name')
                ->get(['id', 'name', 'number_kak', 'club_id']);
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            $trainerClubId = optional($user->profiles[0] ?? null)->club_id;
            $clubs = Club::where('id', $trainerClubId)->get(['id', 'name']);
            $members = Profile::with('club')
                ->where('club_id', $trainerClubId)
                ->where('is_treinador', false)
                ->orderBy('name')
                ->get(['id', 'name', 'number_kak', 'club_id']);
        } else {
            abort(403);
        }

        return view('trainers.create', compact('members', 'clubs'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'profile_id' => ['required','integer','exists:profiles,id'],
            'training_club_ids' => ['nullable','array'],
            'training_club_ids.*' => ['integer','exists:clubs,id'],
        ]);

        $profile = Profile::query()->with('trainingClubs')->findOrFail($data['profile_id']);

        $profile->is_treinador = true;
        $profile->save();

        // incluir o clube principal no conjunto a sincronizar
        $toSync = collect($data['training_club_ids'] ?? [])
            ->push($profile->club_id)
            ->filter()       // remove null
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        $profile->trainingClubs()->sync($toSync);

        return response()->json(['ok' => true]);
    }

    public function edit(Profile $trainer)
    {
        if (!$trainer->is_treinador) {
            abort(404);
        }

        $user = auth()->user();
        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            // ok
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            $trainerClubId = optional($user->profiles[0] ?? null)->club_id;
            if ($trainer->club_id !== $trainerClubId) {
                abort(403);
            }
        } else {
            abort(403);
        }

        return view('trainers.edit', compact('trainer'));
    }

    public function update(\Illuminate\Http\Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['nullable','string','max:255'],
            'is_treinador' => ['required','boolean'],
            'training_club_ids' => ['nullable','array'],
            'training_club_ids.*' => ['integer','exists:clubs,id'],
        ]);

        $profile = Profile::query()->findOrFail($id);

        $profile->fill([
            'name' => $data['name'] ?? $profile->name,
            'is_treinador' => (bool) $data['is_treinador'],
        ])->save();

        // incluir o clube principal no sync
        $toSync = collect($data['training_club_ids'] ?? [])
            ->push($profile->club_id)
            ->filter()
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        $profile->trainingClubs()->sync($toSync);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, Profile $trainer)
    {
        // autorização (mantenha sua lógica atual)
        $user = auth()->user();
        if ($user->hasRole(\App\Role::SUPER_ADMIN->value)) {
            // ok
        } elseif ($user->hasAnyRole([\App\Role::TREINADOR_GRAU_III->value])) {
            // ok conforme sua regra
        } else {
            abort(403);
        }

        // se já não for treinador, só responde
        if (!$trainer->is_treinador) {
            return $request->wantsJson()
                ? response()->json(['ok' => true])
                : redirect()->route('trainers.index')->with('success', 'Treinador removido.');
        }

        // desativa flag e limpa vínculos de clubes (inclusive principal na pivot)
        $trainer->is_treinador = false;
        $trainer->save();
        $trainer->trainingClubs()->detach();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('trainers.index')->with('success', 'Treinador removido.');
    }

    // Busca de membros elegíveis para promoção (não-trainers)
    public function members(Request $request)
    {
        $user = auth()->user();

        $q = Profile::with(['user','club'])
            ->where('is_treinador', false);

        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            if ($request->filled('club_id')) {
                $q->where('club_id', $request->integer('club_id'));
            }
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            $trainerClubId = optional($user->profiles[0] ?? null)->club_id;
            $q->where('club_id', $trainerClubId);
        } else {
            abort(403);
        }

        if ($term = trim((string)$request->input('q', ''))) {
            $q->where(function ($qq) use ($term) {
                $qq->where('name', 'like', "%{$term}%")
                   ->orWhere('number_kak', 'like', "%{$term}%")
                   ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$term}%"));
            });
        }

        $members = $q->orderBy('name')->limit(20)->get();

        return response()->json($members->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->user->email ?? null,
                'number_kak' => $m->number_kak,
                'club' => $m->club->name ?? null,
                'club_id' => $m->club_id, // <-- adicionar
            ];
        }));
    }
}