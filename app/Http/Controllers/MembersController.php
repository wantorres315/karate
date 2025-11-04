<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;        // ajuste se o model tiver outro nome
use App\Models\Graduation;    // opcional, ajuste/remova se não existir
use App\Models\Payment;       // opcional, ajuste/remova se não existir

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
        return view('members.form', compact('clubs', 'users'));
    }

    public function edit($id)
    {
        $member = \App\Models\Profile::findOrFail($id);
        $clubs = \App\Models\Club::orderBy('name')->get();
        $families = \App\Models\Family::orderBy('name')->get();
        return view('members.form', compact('member', 'clubs', 'families'));
    }

    // outros métodos (store, update, destroy) conforme necessário...
}