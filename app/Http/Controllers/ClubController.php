<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;

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

        $clubs->appends($request->only(['name','sigla']));

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
            'sigla' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
        ]);

        $club->update($request->only('name','sigla','location'));

        return redirect()->route('clubs.index', $request->only(['page','name','sigla']))
                         ->with('success', 'Clube atualizado com sucesso!');
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
}
