<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Club;
use App\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Graduation;
use App\Models\GraduationUser;


class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['profile.club', 'lastGraduation.graduation']);
        if (!auth()->user()->hasRole(Role::SUPER_ADMIN->value)) {
            if (
                auth()->user()->hasRole(Role::TREINADOR_GRAU_I->value) ||
                auth()->user()->hasRole(Role::TREINADOR_GRAU_II->value) ||
                auth()->user()->hasRole(Role::TREINADOR_GRAU_III->value) ||
                auth()->user()->hasRole(Role::ARBITRATOR->value)
            ) {
                $clubIds = auth()->user()->clubsAsInstructor()->pluck('clubs.id'); // pega IDs dos clubes do instrutor
                $query->whereHas('profile', function ($q) use ($clubIds) {
                    $q->whereIn('club_id', $clubIds);
                });
            }
        }
        // 🔍 Filtro por nome
        if ($request->filled('nome')) {
            $query->where('name', 'like', '%' . $request->nome . '%');
        }

        // 🔍 Filtro por número KAK
        if ($request->filled('number_kak')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->whereRaw("REPLACE(number_kak, '.', '') LIKE ?", ['%' . str_replace('.', '', $request->number_kak) . '%']);
            });
        }

        // 🔍 Filtro por clube
        if ($request->filled('clube')) {
            $query->whereHas('profile.club', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->clube . '%');
            });
        }
       
        $alunos = $query->orderBy("id", "desc")->paginate(10)->through(function ($user) {
            return [
                'id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
                'clube' => $user->profile->club ? $user->profile->club->acronym : 'No Club',
                'clube_name' => $user->profile->club ? $user->profile->club->name : 'No Club',
                'number_kak' => $user->profile->number_kak,
                'graduacao' => $user->lastGraduation?->graduation->name ?? 'Sem graduação',
                'graduacao_data' => $user->lastGraduation?->date,
                'graduacao_color' => $user->lastGraduation?->graduation->color ?? '#ccc',
            ];
        });
        
        return view('student.index', compact('alunos'));
    }

    public function create()
    {
        $clubs = Club::all();
        return view('student.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'photo' => 'nullable|image|max:2048',
            'number_kak' => 'nullable|string|max:255',
            'number_fnkp' => 'nullable|string|max:255',
            'admission_date' => 'nullable|date',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'document_type' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'cell_number' => 'nullable|string|max:20',
            'contact' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'observations' => 'nullable|string',
            'club_id' => 'nullable|exists:clubs,id',
        ]);

        // Cria o usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Upload da foto
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles', 'public');
        }

        $clubId = str_pad($request->club_id, 2, 0, STR_PAD_LEFT);
        
        $lastUser = User::whereHas('profile', function ($q) use ($clubId) {
            $q->where('club_id', $clubId);
        })
        ->orderBy('id', 'desc')
        ->first();

        $newNumber = 1;

        // Se já existe usuário, pega o number_kak dele, senão começa do 0
        if ($lastUser && $lastUser->profile) {   
            // Remove o club_id da frente e pega só o número sequencial
            $lastNumber = str_replace(".", "", str_replace($clubId, '', $lastUser->profile->number_kak));
            $newNumber = intval($lastNumber) + 1;
        } 

        $numberSum = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        $newNumberKak = $clubId . $numberSum;
        // Cria o perfil
        $profile = new Profile([
            'number_kak' => $newNumberKak,
            'number_fnkp' => $request->number_fnkp,
            'admission_date' => $request->admission_date,
            'photo' => $photoPath,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'birth_date' => $request->birth_date,
            'nationality' => $request->nationality,
            'profession' => $request->profession,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'phone_number' => $request->phone_number,
            'cell_number' => $request->cell_number,
            'contact' => $request->contact,
            'contact_number' => $request->contact_number,
            'contact_email' => $request->contact_email,
            'observations' => $request->observations,
            'status' => 'active', // status default
            'club_id' => $request->club_id,
        ]);

        $user->profile()->save($profile);

        $user->assignRole(Role::PRATICANTE->value);

        return redirect()->route('student.index')
                         ->with('success', 'Usuário e perfil criados com sucesso!');
    }

    public function edit($id)
    {
        $clubs = Club::all();
        $user = User::with('profile.club')->findOrFail($id);

        return view('student.edit', compact('user', 'clubs'));
    }

    public function update(Request $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);

        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'photo' => 'nullable|image|max:2048',
            'number_kak' => 'nullable|string|max:255',
            'number_fnkp' => 'nullable|string|max:255',
            'admission_date' => 'nullable|date',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'document_type' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'cell_number' => 'nullable|string|max:20',
            'contact' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'observations' => 'nullable|string',
            'club_id' => 'nullable|exists:clubs,id',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles', 'public');
            // Remove a foto antiga se existir
            if ($user->profile->photo) {
                \Storage::disk('public')->delete($user->profile->photo);
            }
        }

        // Atualiza os dados do usuário
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Atualiza os dados do perfil
        $user->profile->update([
            'number_kak' => $request->number_kak,
            'number_fnkp' => $request->number_fnkp,
            'admission_date' => $request->admission_date,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'birth_date' => $request->birth_date,
            'nationality' => $request->nationality,
            'profession' => $request->profession,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'phone_number' => $request->phone_number,
            'cell_number' => $request->cell_number,
            'contact' => $request->contact,
            'contact_number' => $request->contact_number,
            'contact_email' => $request->contact_email,
            'observations' => $request->observations,
            'club_id' => $request->club_id,
            'photo' => $photoPath ?? $user->profile->photo,
        ]);

        return redirect()->route('student.edit', $user->id)
                         ->with('success', 'Usuário e perfil atualizados com sucesso!');
    }

    // Rota para validar email via AJAX
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Remove a foto do perfil se existir
        if ($user->profile && $user->profile->photo) {
            \Storage::disk('public')->delete($user->profile->photo);
        }

        $user->delete();

        return redirect()->route('student.index')
                         ->with('success', 'Usuário e perfil deletados com sucesso!');
    }

    public function graduations(User $user)
    {
        $graduacoes = $user->graduations()
            ->with('graduation')
            ->orderByDesc('date')
            ->get();

        $todasGraduacoes = Graduation::all();
        return view('student.graduation', compact('user', 'graduacoes', 'todasGraduacoes'));
    }

    public function addGraduation(Request $request, User $user)
    {
        $request->validate([
            'graduation_id' => 'required|exists:graduations,id',
            'date' => 'required|date',
        ]);

        GraduationUser::create([
            'user_id' => $user->id,
            'graduation_id' => $request->graduation_id,
            'date' => $request->date,
            'value' => $request->value,
            'kihon' => $request->kihon,
            'kata' => $request->kata,
            'kumite' => $request->kumite,
            'location' => $request->location,
        ]);

        return redirect()->route('student.graduations', $user)
            ->with('success', 'Graduação adicionada com sucesso!');
    }

    public function removeGraduation(User $user, GraduationUser $graduationUser)
    {
        $graduationUser->delete();

        return redirect()->route('student.graduations', $user)
            ->with('success', 'Graduação removida com sucesso!');
    }
}


