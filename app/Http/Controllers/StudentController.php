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


class StudentController extends Controller
{
    public function index(Request $request)
    {
        $graduacoes = Graduation::all();

        $query = User::with(['profiles.club', 'profiles.lastGraduation.graduation']);
        $user = auth()->user();

        if (!$user->hasRole(Role::SUPER_ADMIN->value)) {
           if ($user->hasRole(Role::PRATICANTE->value)) {
                 $query->where('id', $user->id);
            } elseif ($user->hasAnyRole([
                Role::TREINADOR_GRAU_I->value,
                Role::TREINADOR_GRAU_II->value,
                Role::TREINADOR_GRAU_III->value,
                Role::ARBITRATOR->value,
            ])) {
                $clubIds = auth()->user()->clubsAsInstructor()->pluck('clubs.id');
                $query->whereHas('profiles', function ($q) use ($clubIds) {
                    $q->whereIn('club_id', $clubIds);
                });
            }
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
                $alunos->push([
                    'user_id' => $user->id,
                    'profile_id' => $profile->id,
                    'nome' => $profile->name,
                    'user_email' => $user->email,
                    'clube' => $profile->club ? $profile->club->acronym : 'No Club',
                    'clube_name' => $profile->club ? $profile->club->name : 'No Club',
                    'number_kak' => $profile->number_kak,
                    'graduacao' => $profile->lastGraduation?->graduation->name ?? 'Sem graduação',
                    'graduacao_data' => $profile->lastGraduation?->date,
                    'graduacao_color' => $profile->lastGraduation?->graduation->color ?? '#ccc',
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

        return view('student.index', [
            'alunos' => $paginated,
            'graduacoes' => $graduacoes,
        ]);
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
        if ($request->filled('photo_data')) {
            try {
                $photoData = $request->input('photo_data');

                if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                    $photoData = substr($photoData, strpos($photoData, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                        return Redirect::back()->withErrors(['photo_data' => 'Formato de imagem inválido.']);
                    }

                    $photoData = base64_decode($photoData);

                    if ($photoData === false) {
                        return Redirect::back()->withErrors(['photo_data' => 'Erro ao decodificar imagem.']);
                    }

                    // Garante que o diretório existe
                    $directory = 'profile_photos';
                    if (!Storage::exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory, 0755, true);
                    }
                    
                    $fileName = 'profile_' . $user->id . '_' . time() . '.' . $type;
                    $filePath = $directory . '/' . $fileName;
                    
                    // Salva usando o disco 'public' e o método put()
                    if (Storage::disk('public')->put($filePath, $photoData)) {
                        // Remove a foto antiga se existir
                        if ($user->photo) {
                            $oldPhotoPath = str_replace('/storage/', 'public/', $user->photo);
                            if (Storage::disk('public')->exists($oldPhotoPath)) {
                                Storage::disk('public')->delete($oldPhotoPath);
                            }
                        }
                        // Salva o caminho relativo para a nova foto
                        $user->photo = '/storage/profile_photos/' . $fileName;
                    } else {
                        throw new \Exception('Falha ao salvar a imagem.');
                    }
                } else {
                    return Redirect::back()->withErrors(['photo_data' => 'Formato de imagem inválido.']);
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao salvar a foto de perfil: ' . $e->getMessage());
                return Redirect::back()->withErrors(['photo_data' => 'Erro ao processar a imagem. Por favor, tente novamente.']);
            }
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

    public function edit( Profile $profile)
    {
        $clubs = Club::all();
        $profile->load("user");
        return view('student.edit', compact('profile', 'clubs'));
    }

    public function update(Request $request, Profile $profile)
    {

        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
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
        if ($request->filled('photo_data')) {
            $photoData = $request->input('photo_data');
            if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                $photoData = substr($photoData, strpos($photoData, ',') + 1);
                $type = strtolower($type[1]);
                $photoData = base64_decode($photoData);

                $directory = 'profile_photos';
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory, 0755, true);
                }

                $fileName = 'profile_' . $profile->id . '_' . time() . '.' . $type;
                $filePath = $directory . '/' . $fileName;
                Storage::disk('public')->put($filePath, $photoData);

                // Apaga foto antiga
                if($profile->photo){
                    $old = str_replace('/storage/','public/',$profile->photo);
                    Storage::disk('public')->delete($old);
                }

                $photo = '/storage/' . $filePath;
            }
        }

       
        // Atualiza os dados do perfil
        $profile->update([
            'name' => $request->name,
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
            'club_id' => $request->club_id ?? $profile->club_id,
            'photo' => $photo ?? $profile->photo,
        ]);

        return redirect()->route('student.edit', $profile->id)
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

    public function graduations(Profile $profile)
    {
        $graduacoes = $profile->graduations()
            ->with('graduation')
            ->orderByDesc('date')
            ->get();

        $todasGraduacoes = Graduation::all();
        return view('student.graduation', compact('profile', 'graduacoes', 'todasGraduacoes'));
    }

    public function addGraduation(Request $request, Profile $profile)
    {
        $request->validate([
            'graduation_id' => 'required|exists:graduations,id',
            'date' => 'required|date',
        ]);

        GraduationUser::create([
            'profile_id' => $profile->id,
            'graduation_id' => $request->graduation_id,
            'date' => $request->date,
            'value' => $request->value,
            'kihon' => $request->kihon,
            'kata' => $request->kata,
            'kumite' => $request->kumite,
            'location' => $request->location,
        ]);

        return redirect()->route('student.graduations', $profile)
            ->with('success', 'Graduação adicionada com sucesso!');
    }

    public function removeGraduation(Profile $profile, GraduationUser $graduationUser)
    {
        $graduationUser->delete();

        return redirect()->route('student.graduations', $profile)
            ->with('success', 'Graduação removida com sucesso!');
    }
}


