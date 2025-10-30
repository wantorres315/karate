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

   public function store(Request $request)
{
    // 2️⃣ Checar se usuário já existe
    $user = User::where('email', $request->email)->first();

    // 3️⃣ Se não existir, criar usuário
    if (!$user) {
        // Gerar number_kak para senha temporária
        $clubId = str_pad($request->club_id ?? 0, 2, '0', STR_PAD_LEFT);

        $lastProfile = Profile::where('club_id', $request->club_id)
                              ->orderBy('id', 'desc')
                              ->first();

        $newNumber = 1;
        if ($lastProfile) {
            $lastNumber = str_replace($clubId, '', str_replace('.', '', $lastProfile->number_kak));
            $newNumber = intval($lastNumber) + 1;
        }
        $numberSum = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        $newNumberKak = $clubId.$numberSum;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($newNumberKak), // senha inicial
        ]);

        // Atribuir role
        $user->assignRole(Role::PRATICANTE->value);
    }

    // 4️⃣ Processar foto (Base64) se existir
    $photoPath = null;
    if ($request->filled('photo_data')) {
        $photoData = $request->photo_data;
        if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
            $photoData = substr($photoData, strpos($photoData, ',') + 1);
            $type = strtolower($type[1]);
            $photoData = base64_decode($photoData);

            $directory = 'profile_photos';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }

            $fileName = 'profile_'.$user->id.'_'.time().'.'.$type;
            $filePath = $directory.'/'.$fileName;
            Storage::disk('public')->put($filePath, $photoData);

            $photoPath = '/storage/'.$filePath;
        }
    }

    // 5️⃣ Gerar number_kak para o profile
    $clubId = str_pad($request->club_id ?? 0, 2, '0', STR_PAD_LEFT);
    $lastProfile = Profile::where('club_id', $request->club_id)
                          ->orderBy('id', 'desc')
                          ->first();

    $newNumber = 1;
    if ($lastProfile) {
        $lastNumber = str_replace($clubId, '', str_replace('.', '', $lastProfile->number_kak));
        $newNumber = intval($lastNumber) + 1;
    }
    $numberSum = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    $newNumberKak = $clubId.$numberSum;

    // 6️⃣ Criar profile associado ao usuário existente ou novo
    $profile = Profile::create([
        'user_id' => $user->id,
        'name' => $request->name,
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
        'status' => 'active',
        'club_id' => $request->club_id,
        
    ]);

    // 7️⃣ Criar graduação inicial
    $graduation = Graduation::where('name', '9º KYU')->first();
    if ($graduation) {
        GraduationUser::create([
            'graduation_id' => $graduation->id,
            'profile_id' => $profile->id,
            'date' => Carbon::now(),
        ]);
    }

    return redirect()->route('members.index')
                     ->with('success', 'Perfil criado com sucesso!');
}

public function toggleTreinador(Request $request, Profile $profile)
{
    if($request["grau"] !== "no_rule"){
        $isTreinador = true;
    }else{
        if($profile->is_treinador === true){
            $isTreinador = false;
        }else{
            $isTreinador = true;
        } 
    }
    $profile->update(['is_treinador' => $isTreinador]);

    $roleName = match ($request['grau']) {
        "no_rule" => null,
        'I' => Role::TREINADOR_GRAU_I->value,
        'II' => Role::TREINADOR_GRAU_II->value,
        'III' => Role::TREINADOR_GRAU_III->value,
    };

    $profile->user->removeRole([
        Role::TREINADOR_GRAU_I->value,
        Role::TREINADOR_GRAU_II->value,
        Role::TREINADOR_GRAU_III->value,
    ]);
    ClubInstructors::where("club_id", $profile->club_id)->where("profile_id", $profile->id)->delete();

    if($roleName !== null){
        ClubInstructors::updateOrCreate(
                [
                    'club_id' => $profile->club_id,
                    'profile_id' => $profile->id,
                ],
                [] // sem campos adicionais, só garante a existência
        );
        $profile->user->assignRole($roleName);
    }

    // 🔥 MANTÉM OS FILTROS DA URL ORIGINAL
    $query = $request->only(['nome', 'number_kak', 'clube', 'graduacao_id']);

    return redirect()
        ->route('members.index', array_filter($query))
        ->with('success', $isTreinador ? "Treinador Grau {$request['grau']} atribuído." : 'Treinador removido.');
}




    public function edit( Profile $profile)
    {
        $clubs = Club::all();
        $profile->load("user");
        return view('members.edit', compact('profile', 'clubs'));
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

        return redirect()->route('members.edit', $profile->id)
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

    public function destroy(Profile $profile)
    {
        // Remove a foto do perfil se existir
        if ($profile && $profile->photo) {
            \Storage::disk('public')->delete($profile->photo);
        }

        $profile->delete();

        return redirect()->route('members.index')
                         ->with('success', 'Usuário deletado com sucesso!');
    }

    public function graduations(Profile $profile)
    {
        $graduacoes = $profile->graduations()
            ->with('graduation')
            ->orderByDesc('date')
            ->get();

        $todasGraduacoes = Graduation::all();
        return view('members.graduation', compact('profile', 'graduacoes', 'todasGraduacoes'));
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

        return redirect()->route('members.graduations', $profile)
            ->with('success', 'Graduação adicionada com sucesso!');
    }

    public function removeGraduation(Profile $profile, GraduationUser $graduationUser)
    {
        $graduationUser->delete();

        return redirect()->route('members.graduations', $profile)
            ->with('success', 'Graduação removida com sucesso!');
    }
}


