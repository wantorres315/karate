<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\ClubInstructors;
use App\Role;

class ClubController extends Controller
{
    /**
     * Listar clubes com filtros
     */
    public function index(Request $request)
    {
        $clubs = Club::query()
            ->when($request->filled('name'), fn($q) =>
                $q->where('name', 'like', '%' . $request->name . '%')
            )
            ->when($request->filled('sigla'), fn($q) =>
                $q->where('sigla', 'like', '%' . $request->sigla . '%')
            )
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $clubs->appends($request->only(['name','sigla', "logo"]));

        return view('clubs.index', compact('clubs'));
    }

    /**
     * Form de criação
     */
    public function create()
    {
        return view('clubs.create');
    }

    /**
     * Salvar novo clube
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:clubs,name',
            'acronym' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'username_fnkp' => 'nullable|string|max:255',
            'username_password_fnkp' => 'nullable|string|max:255',
            'certificate_fnkp' => 'nullable|string|max:255',
            'status_year' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'cell_number' => 'nullable|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'responsible_name' => 'nullable|string|max:255',
            'responsible_cell_number' => 'nullable|string|max:20',
            'responsible_telephone_number' => 'nullable|string|max:20',
            'responsible_position' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'name',
            'acronym',
            'logo',
            'username_fnkp',
            'username_password_fnkp',
            'certificate_fnkp',
            'status_year',
            'status',
            'address',
            'postal_code',
            'city',
            'district',
            'cell_number',
            'phone_number',
            'email',
            'website',
            'responsible_name',
            'responsible_cell_number',
            'responsible_telephone_number',
            'responsible_position',
        ]);

        // Se enviou logo, salvar arquivo
        
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('clubs', 'public'); 
            $data['logo'] = $path;
        }

        Club::create($data);

        return redirect()->route('clubs.index');
    }


    /**
     * Form de edição
     */
    public function edit(Club $club, Request $request)
    {
        $page = $request->page ?? 1;
        return view('clubs.edit', compact('club','page'));
    }

    /**
     * Atualizar clube
     */
    public function update(Request $request, Club $club)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:clubs,name,' . $club->id,
        'acronym' => 'nullable|string|max:20',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'username_fnkp' => 'nullable|string|max:255',
        'username_password_fnkp' => 'nullable|string|max:255',
        'certificate_fnkp' => 'nullable|string|max:255',
        'status_year' => 'nullable|string|max:10',
        'status' => 'nullable|in:active,inactive',
        'address' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        'city' => 'nullable|string|max:100',
        'district' => 'nullable|string|max:100',
        'cell_number' => 'nullable|string|max:20',
        'phone_number' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'website' => 'nullable|string|max:255',
        'responsible_name' => 'nullable|string|max:255',
        'responsible_cell_number' => 'nullable|string|max:20',
        'responsible_telephone_number' => 'nullable|string|max:20',
        'responsible_position' => 'nullable|string|max:100',
    ]);

    $data = $request->only([
        'name', 'acronym', 'username_fnkp', 'username_password_fnkp',
        'certificate_fnkp', 'status_year', 'status', 'address', 'postal_code',
        'city', 'district', 'cell_number', 'phone_number', 'email', 'website',
        'responsible_name', 'responsible_cell_number', 'responsible_telephone_number',
        'responsible_position'
    ]);

    // Se enviar logo, salvar arquivo
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('clubs/logos', 'public');
        $data['logo'] = $logoPath;
    }

    $club->update($data);

    return redirect()->route('clubs.index');
}


    /**
     * Deletar clube
     */
    public function destroy(Request $request, Club $club)
    {
        $club->delete();

        return redirect()->route('clubs.index', $request->only(['page','name','sigla']))
                         ->with('success', 'Clube excluído com sucesso!');
    }

    public function addCoach(Club $club)
    {
        $students = $club->profiles()
            ->where("is_treinador", false)
            ->orderBy('name')
            ->get();

         $coaches = $club->instructors()->get();

        return view('clubs.add-coach', compact('club', 'students', "coaches"));
    }

   public function storeCoach(Request $request, Club $club)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:profiles,id',
        ]);
        // Evita duplicar treinador
        if ($club->instructors()->where('profile_id', $validated['student_id'])->exists()) {
            return back()->withErrors(['student_id' => 'Este treinador já está associado ao clube.']);
        }

        // Associa o treinador ao clube
        $club->instructors()->attach($validated['student_id']);

        $profile = Profile::find($validated["student_id"]);
        
        $profile->user->assignRole(Role::TREINADOR_GRAU_I->value);

        $profile->is_treinador = true;
        $profile->save();

        return back()->with('success', 'Treinador adicionado com sucesso!');
    }

    public function removeCoach(Club $club, Profile $profile)
    {
        $instructor = ClubInstructors::where("club_id", $club->id)->where("profile_id", $profile->id)->delete();

        $profile->update(["is_treinador", false]);

        return back()->with('success', 'Treinador removido com sucesso!');
    }
    
    public function members(Club $club)
    {
        $profiles = $club->profiles()
        ->select('id', 'name', 'number_kak', 'birth_date') // precisa do birth_date para calcular
        ->get()
        ->each(function ($p) {
            $p->append('escalao'); // adiciona o accessor ao JSON
        });
        
        $instructors = $club->instructors()
    ->with('user.roles') // carrega roles do user
    ->select('profiles.id', 'name')
    ->get()
    ->map(function($profile) {
        // pega a primeira role se existir user e roles, senão "Treinador"
        $profile->role = $profile->user?->roles->pluck('name')->first() ?? 'Treinador';
        unset($profile->user); // remove user do JSON
        return $profile;
    });

        return response()->json([
            'students' => $profiles,
            'instructors' => $instructors,
        ]);
    }
}
