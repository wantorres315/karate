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
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\ClubInstructors;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        
        $graduacoes = Graduation::all();

        $query = User::with(['profiles.club', 'profiles.lastGraduation.graduation']);
        $user = auth()->user();
       if (!$user->hasRole(Role::SUPER_ADMIN->value)) {

            $query->where(function ($q) use ($user) {

                // Se for praticante, pode ver apenas a si mesmo
                if ($user->hasRole(Role::PRATICANTE->value)) {
                    $q->orWhere('id', $user->id);
                }

                // Se for treinador, pode ver alunos dos clubes que instrui
                if ($user->hasAnyRole([
                    Role::TREINADOR_GRAU_I->value,
                    Role::TREINADOR_GRAU_II->value,
                    Role::TREINADOR_GRAU_III->value,
                    Role::ARBITRATOR->value,
                ])) {
                    $clubIds = $user->clubsAsInstructor()->pluck('clubs.id');
                    $q->orWhereHas('profiles', function ($q2) use ($clubIds) {
                        $q2->whereIn('club_id', $clubIds);
                    });
                }

            });

        }

        // 🔍 Filtros
        if ($request->filled('nome')) {
            $query->whereHas('profiles', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nome . '%');
            });
        }

        if ($request->filled('number_kak')) {
            $query->whereHas('profiles', function ($q) use ($request) {
                $q->whereRaw("REPLACE(number_kak, '.', '') LIKE ?", ['%' . str_replace('.', '', $request->number_kak) . '%']);
            });
        }

        if ($request->filled('clube')) {
            $query->whereHas('profiles.club', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->clube . '%');
            });
        }

        if ($request->filled('graduacao_id')) {
            $query->whereHas('profiles.lastGraduation', function ($q) use ($request) {
                $q->where('graduation_id', $request->graduacao_id);
            });
        }

        // Paginação
        $users = $query->orderBy("id", "desc")->get();

        $alunos = collect();
        foreach ($users as $user) {
            foreach ($user->profiles as $profile) {
                $photoPath =  asset( $profile->photo); 
                $alunos->push([
                    'user_id' => $user->id,
                    'profile_id' => $profile->id,
                    'nome' => $profile->name,
                    'user_email' => $user->email,
                    'photo' => $photoPath,
                    'clube' => $profile->club ? $profile->club->acronym : 'No Club',
                    'clube_name' => $profile->club ? $profile->club->name : 'No Club',
                    'number_kak' => $profile->number_kak,
                    'graduacao' => $profile->lastGraduation?->graduation->name ?? 'Sem graduação',
                    'graduacao_data' => $profile->lastGraduation?->date,
                    'graduacao_color' => $profile->lastGraduation?->graduation->color ?? '#ccc',
                    'escalao' => $profile->is_treinador == true ? "Treinador" : $profile->escalao,
                    'is_treinador' => $profile->is_treinador,
                    "user" => $user,
                ]);
            }
        }

        $page = $request->get('page', 1);
        $perPage = 10;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $alunos->forPage($page, $perPage),
            $alunos->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return view('members.index', [
            'alunos' => $paginated,
            'graduacoes' => $graduacoes,
            
        ]);
    }

    public function create()
    {
        $clubs = Club::all();
        return view('members.create', compact('clubs'));
    }

  

    public function edit( Profile $profile)
    {
        $clubs = Club::all();
        $profile->load("user");
        return view('members.edit', compact('profile', 'clubs'));
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

    

    
}


