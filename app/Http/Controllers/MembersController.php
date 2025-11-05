<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;        // ajuste se o model tiver outro nome
use App\Models\Graduation;    // opcional, ajuste/remova se não existir
use App\Models\Payment;       // opcional, ajuste/remova se não existir
use App\Models\GraduationUser; // modelo pivô para graduações
use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class MembersController extends Controller
{
    public function index(Request $request)
    {
        // Base query
        $q = Profile::join('users', 'profiles.user_id', '=', 'users.id')
            ->leftJoin('clubs', 'profiles.club_id', '=', 'clubs.id')
            ->select('profiles.*', 'users.email as user_email')
            ;

        // busca geral (campo q) — procura em nome, email e number_kak

        if ($request->filled('q')) {
            $term = $request->input('q');
            $q->where(function($r) use ($term) {
                $r->where('profiles.name', 'like', "%{$term}%")
                  ->orWhere('users.email', 'like', "%{$term}%")
                  ->orWhere('number_kak', 'like', "%{$term}%")
                  ->orWhere('clubs.name', 'like', "%{$term}%");
            });
        }

        // ordenação e paginação
        $alunos = $q->orderBy('name')->paginate(20)->appends($request->query());
        // dados auxiliares para a view — ajuste conforme modelos reais
        $graduacoes = class_exists(Graduation::class) ? Graduation::orderBy('id')->get() : collect();
        $recentPayments = class_exists(Payment::class) ? Payment::latest()->take(5)->get() : collect();
        
        return view('members.index', compact('alunos', 'graduacoes', 'recentPayments'));
    }

    public function create()
    {
        $clubs = \App\Models\Club::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
         $families = \App\Models\Family::orderBy('name')->get();
        return view('members.form', compact('clubs', 'users', 'families'));
    }

    public function edit($id)
    {
        $member = \App\Models\Profile::findOrFail($id);
        $clubs = \App\Models\Club::orderBy('name')->get();
        $families = \App\Models\Family::orderBy('name')->get();
        return view('members.form', compact('member', 'clubs', 'families'));
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
        "postal_code" => $request->postal_code,
        "nif" => $request->nif,
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
            "postal_code" => 'nullable|string|max:20',
            "nif" => 'nullable|string|max:20',
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
            "postal_code" => $request->postal_code,
            "nif" => $request->nif,
        ]);

        return redirect()->route('members.edit', $profile->id)
                         ->with('success', 'Usuário e perfil atualizados com sucesso!');
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
    // outros métodos (store, update, destroy) conforme necessário...
}