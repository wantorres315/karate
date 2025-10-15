<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\Classe;
use Carbon\Carbon;
use App\Role;
use App\Models\ClassLesson;
use App\Models\Attendance;

class ClasseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            $classes = Classe::with(['club', 'instructors'])
                ->paginate(10);

        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            // Treinador vê apenas as classes do seu clube
            $classes = Classe::with(['club', 'instructors'])
                ->where('club_id', $user->profiles[0]->club_id)
                ->paginate(10);

        } else {
            // Outros papéis → nenhum acesso
            $classes = collect(); // coleção vazia
        }
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            // Pode ver todos
            $clubs = Club::all();
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            // Ver apenas o clube onde está alocado
            
            $clubs = Club::whereHas('instructors', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        } else {
            // Outros papéis (caso queira limitar mais)
            $clubs = collect(); // retorna coleção vazia
        }

        return view('classes.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'club_id' => 'required|exists:clubs,id',
            'week_days' => 'nullable|array',
            'students' => 'nullable|array',
            'instructors' => 'nullable|array', // <-- adiciona instrutores
        ]);

        
        $classe = Classe::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            "startDate" => $request->start_date,
            "endDate" => $request->end_date,
            'club_id' => $request->club_id,
            'week_days' => json_encode($request->week_days ?? []),
        ]);

        $classe->students()->sync($request->students ?? []);

        $classe->instructors()->sync($request->instructors ?? []);

        $classe->generateLessons();

        return redirect()->route('classes.index')->with('success', 'Turma criada com sucesso!');
    }

    public function edit(Classe $classe)
    {
        $clubs = Club::all();

        // IDs dos alunos já associados à turma (desambiguação)
        $selectedStudents = $classe->students()->pluck('profiles.id')->toArray();

        $selectedInstructors = [];

        // Para editar
       $instructors = Profile::where('club_id', $classe->club_id)
        ->where('is_treinador', true)
        ->whereHas('user', function ($query) {
            $query->role([
                Role::TREINADOR_GRAU_I->value,
                Role::TREINADOR_GRAU_II->value,
                Role::TREINADOR_GRAU_III->value
            ]);
        })
        ->get();
        
        $selectedInstructors = $classe->instructors()->pluck('profiles.id')->toArray();

        // Alunos do mesmo clube
        $students = Profile::where('club_id', $classe->club_id)
            ->with('user')
            ->orderBy('number_kak')
            ->get();

        return view('classes.edit', compact('classe', 'clubs', 'students', 'selectedStudents', "instructors", "selectedInstructors"));
    }


    public function update(Request $request, Classe $classe)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'club_id' => 'required|exists:clubs,id',
            'week_days' => 'nullable|array',
            'students' => 'nullable|array',
            'instructors' => 'nullable|array', // <-- adiciona instrutores
        ]);

        // Atualiza a turma
        $classe->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'club_id' => $request->club_id,
            'week_days' => json_encode($request->week_days ?? []),
        ]);
        $classe->update($request->all());

        // gera todas as aulas novas
        $classe->generateLessons();

        $classe->students()->sync($request->students ?? []);

        $classe->instructors()->sync($request->instructors ?? []);

        return redirect()->route('classes.index')->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(Classe $classe)
    {
        $classe->students()->detach(); // Remove relações
        $classe->delete();

        return redirect()->route('classes.index')->with('success', 'Turma excluída com sucesso!');
    }

    /**
     * Retorna os alunos de um clube em JSON, com escalão calculado.
     */
    public function getStudents($clubId)
    {
        $students = Profile::where('club_id', $clubId)
            ->with('user')
            ->orderBy('number_kak')
            ->get(['id', 'number_kak', 'name', 'birth_date']);

        return response()->json(
            $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'number_kak' => $s->number_kak,
                'escalao' => $s->escalao, // precisa do accessor no Profile
            ])
        );
    }

    public function attendance(Classe $classe)
    {
        $students = $classe->students;
        $lessons = $classe->lessons()->orderBy('lesson_date')->get();

        return view('classes.attendance', compact('classe', 'students', 'lessons'));
    }

    public function saveAttendance(Request $request, Classe $classe)
    {
        foreach ($classe->lessons as $lesson) {
            // 1️⃣ marca todos como ausente
            $lesson->attendances()->update(['present' => 0]);

            // 2️⃣ marca como presente os que foram checados
            if (!empty($request->attendance[$lesson->id])) {
                foreach ($request->attendance[$lesson->id] as $studentId => $present) {
                    Attendance::updateOrCreate(
                        ['class_lesson_id' => $lesson->id, 'student_id' => $studentId],
                        ['present' => 1] // só os marcados ficam presentes
                    );
                }
            }
        }

        return back()->with('success', 'Presenças salvas com sucesso!');
    }
}
