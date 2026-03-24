<?php

namespace App\Http\Controllers;

use App\Models\Style;
use Illuminate\Http\Request;

class StyleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $styles = Style::orderBy('name')->get();
        return view('config.style.index', compact('styles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('config.style.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:styles,name',
        ]);

        Style::create($validated);

        return redirect()->route('config.style.index')
            ->with('success', 'Estilo criado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Style $style)
    {
        return view('config.style.edit', compact('style'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Style $style)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:styles,name,' . $style->id,
        ]);

        $style->update($validated);

        return redirect()->route('config.style.index')
            ->with('success', 'Estilo atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Style $style)
    {
        $style->delete();

        return redirect()->route('config.style.index')
            ->with('success', 'Estilo excluído com sucesso.');
    }
}