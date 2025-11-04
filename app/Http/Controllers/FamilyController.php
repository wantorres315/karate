<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Family;
use App\Models\User;

class FamilyController extends Controller
{
    public function index()
    {
        $families = Family::all();
        return view('families.index', compact('families'));
    }

    public function create()
    {
        return view('families.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Verifica se já existe usuário com este email
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Atualiza o nome do usuário existente e a senha
            $user->update(['name' => $request->name, 'password' => bcrypt('familia')]);
        } else {
            // Cria novo usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt('familia'),
            ]);
        }

        // Cria a família vinculando ao usuário
        $family = Family::create([
            'name' => $request->name,
            'user_id' => $user->id,
        ]);

        return redirect()->route('familias.index')->with('success', 'Família criada ou vinculada com sucesso!');
    }

    public function edit(Family $family)
    {
        return view('families.edit', compact('family'));
    }

    public function update(Request $request, Family $family)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Atualiza usuário familiar
        $user = $family->user;
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Atualiza família
        $family->update([
            'name' => $request->name,
            'user_id' => $user->id,
        ]);

        return redirect()->route('familias.index')->with('success', 'Família atualizada com sucesso!');
    }

    public function destroy(Family $family)
    {
        $family->delete();
        return redirect()->route('familias.index')->with('success', 'Família removida com sucesso!');
    }

    // Métodos extras conforme suas rotas
    public function members(Family $family)
    {
        // Implemente conforme sua lógica
        return view('families.members', compact('family'));
    }

    public function getStudents(Family $family)
    {
        // Implemente conforme sua lógica
        return response()->json([]);
    }

    public function addCoach(Family $family)
    {
        // Implemente conforme sua lógica
        return view('families.addCoach', compact('family'));
    }

    public function storeCoach(Request $request, Family $family)
    {
        // Implemente conforme sua lógica
        return back()->with('success', 'Treinador adicionado!');
    }

    public function removeCoach(Family $family, $profile)
    {
        // Implemente conforme sua lógica
        return back()->with('success', 'Treinador removido!');
    }

    public function graduations(Family $family)
    {
        // Implemente conforme sua lógica
        return view('families.graduations', compact('family'));
    }

    public function addGraduation(Request $request, Family $family)
    {
        // Implemente conforme sua lógica
        return back()->with('success', 'Graduação adicionada!');
    }

    public function removeGraduation(Family $family, $graduationUser)
    {
        // Implemente conforme sua lógica
        return back()->with('success', 'Graduação removida!');
    }

    public function toggleTreinador(Request $request, Family $family)
    {
        // Implemente conforme sua lógica
        return back()->with('success', 'Status de treinador alterado!');
    }

    public function attendance(Family $family)
    {
        // Implemente conforme sua lógica
        return view('families.attendance', compact('family'));
    }

    public function saveAttendance(Request $request, Family $family)
    {
        // Implemente conforme sua lógica
        return back()->with('success', 'Presença salva!');
    }

    public function memberPdf(Family $family)
    {
        // Implemente conforme sua lógica
        return response()->download('caminho/do/pdf');
    }

    public function resetSenha(Family $family)
    {
        $user = $family->user;
        $user->update([
            'password' => bcrypt('familia'), // nova senha padrão
        ]);
        return redirect()->route('familias.index')->with('success', 'Senha do usuário familiar resetada para "familia".');
    }
}