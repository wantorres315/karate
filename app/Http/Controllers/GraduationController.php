<?php

namespace App\Http\Controllers;

use App\Models\Graduation;
use Illuminate\Http\Request;

class GraduationController extends Controller
{
    /**
     * Listar todas as graduações com filtros.
     */
    public function index(Request $request)
    {
        $graduations = Graduation::query()
            ->when($request->filled('name'), fn($q) =>
                $q->where('name', 'like', '%' . $request->name . '%')
            )
            ->when($request->filled('color'), fn($q) =>
                $q->where('color', 'like', '%' . $request->color . '%')
            )
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Mantém os filtros nos links de paginação
        $graduations->appends($request->only(['name', 'color']));

        return view('graduations.index', compact('graduations'));
    }

    /**
     * Mostrar formulário de criação.
     */
    public function create()
    {
        return view('graduations.create');
    }

    /**
     * Salvar nova graduação.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255|unique:graduations,name',
            'color1' => 'required|string|max:50',
            'color2' => 'nullable|string|max:50',
        ]);

        $color1 = strtolower(trim($request->color1));
        $color2 = strtolower(trim($request->color2));

        $color = $color1;
        if ($color2 && $color1 !== $color2) {
            $color = "{$color1}_{$color2}";
        }

        Graduation::create([
            'name'  => $request->name,
            'color' => $color,
        ]);

        return redirect()->route('graduations.index', $request->only(['name', 'color', 'page']))
                         ->with('success', 'Graduação criada com sucesso!');
    }

    /**
     * Mostrar formulário de edição.
     */
    public function edit(Graduation $graduation, Request $request)
    {
        // Passa também a página atual para o form
        $page = $request->page ?? 1;
        return view('graduations.edit', compact('graduation', 'page'));
    }

    /**
     * Atualizar graduação.
     */
    public function update(Request $request, Graduation $graduation)
    {
        $request->validate([
            'name'   => 'required|string|max:255|unique:graduations,name,' . $graduation->id,
            'color1' => 'required|string|max:50',
            'color2' => 'nullable|string|max:50',
        ]);

        $color1 = strtolower(trim($request->color1));
        $color2 = strtolower(trim($request->color2));

        $color = $color1;
        if ($color2 && $color1 !== $color2) {
            $color = "{$color1}_{$color2}";
        }

        $graduation->update([
            'name'  => $request->name,
            'color' => $color,
        ]);

        // Mantém pagina e filtros
        $redirectParams = $request->only(['page']);

        return redirect()->route('graduations.index', $redirectParams)
                         ->with('success', 'Graduação atualizada com sucesso!');
    }

    /**
     * Deletar graduação.
     */
    public function destroy(Request $request, Graduation $graduation)
    {
        $graduation->delete();

        // Mantém filtros e pagina
        $redirectParams = $request->only(['page', 'name', 'color']);

        return redirect()->route('graduations.index', $redirectParams)
                         ->with('success', 'Graduação excluída com sucesso!');
    }
}
