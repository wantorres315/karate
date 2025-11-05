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
    public function index(Request $request)
    {
        $user = auth()->user();

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        // Base query com relacionamentos usados na view
        $query = Classe::with(['club', 'instructors', 'students']);

        // Clubs para o filtro (como no members)
        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            $clubs = Club::orderBy('name')->get(['id', 'name']);
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            $clubs = Club::where('id', optional($user->profiles[0] ?? null)->club_id)->get(['id', 'name']);
        } else {
            $clubs = collect();
        }

        // Escopo por papel
        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            // opcionalmente filtra por clube recebido
            if ($request->filled('club_id')) {
                $query->where('club_id', $request->input('club_id'));
            }
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            $trainerClubId = optional($user->profiles[0] ?? null)->club_id;
            $query->where('club_id', $trainerClubId);
            // ignora club_id diferente do permitido
        } else {
            // Sem acesso: devolve paginator vazio
            $classes = Classe::with(['club', 'instructors', 'students'])
                ->whereRaw('1=0')
                ->paginate($perPage);
            return view('classes.index', compact('classes', 'clubs'));
        }

        // Pesquisa por nome da turma, descrição, instrutor ou clube
        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhereHas('instructors', function ($qi) use ($term) {
                      $qi->where('name', 'like', "%{$term}%");
                  })
                  ->orWhereHas('club', function ($qc) use ($term) {
                      $qc->where('name', 'like', "%{$term}%");
                  });
            });
        }

        $classes = $query->orderBy('name')->paginate($perPage);

        return view('classes.index', compact('classes', 'clubs'));
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

    public function schedule()
    {
        return view('classes.schedule');
    }

    public function scheduleData()
    {
        $user = auth()->user();

        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            $lessons = ClassLesson::with(['classe.instructors', 'classe.club'])->get();
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            $trainerClubId = optional($user->profiles[0] ?? null)->club_id;
            $lessons = ClassLesson::whereHas('classe', function ($query) use ($trainerClubId) {
                $query->where('club_id', $trainerClubId);
            })->with(['classe.instructors', 'classe.club'])->get();
        } else {
            $lessons = collect();
        }

        $events = $lessons->map(function ($lesson) {
            $classe = $lesson->classe;
            
            $startDateTime = Carbon::parse($lesson->lesson_date . ' ' . $classe->start_time);
            $endDateTime = Carbon::parse($lesson->lesson_date . ' ' . $classe->end_time);

            $color = $this->getClassColor($classe->id);
            
            $instructorNames = $classe->instructors->pluck('name')->join(', ');

            return [
                'id' => $lesson->id,
                'title' => $classe->name,
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $this->darkenColor($color, 20),
                'extendedProps' => [
                    'classe_id' => (int) $classe->id, // Garantir que é número
                    'location' => $classe->club->name ?? '',
                    'instructor' => $instructorNames,
                    'description' => $classe->description ?? '',
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Gera uma cor fixa baseada no ID
     */
    private function getClassColor($id)
    {
        $colors = [
            '#3788d8', // Azul
            '#f59e0b', // Laranja
            '#10b981', // Verde
            '#ef4444', // Vermelho
            '#8b5cf6', // Roxo
            '#ec4899', // Rosa
            '#14b8a6', // Turquesa
            '#f97316', // Laranja escuro
            '#6366f1', // Índigo
            '#84cc16', // Lima
            '#06b6d4', // Ciano
            '#d946ef', // Fúcsia
        ];
        
        return $colors[$id % count($colors)];
    }

    /**
     * Escurece uma cor hexadecimal
     */
    private function darkenColor($hex, $percent)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = max(0, min(255, $r - ($r * $percent / 100)));
        $g = max(0, min(255, $g - ($g * $percent / 100)));
        $b = max(0, min(255, $b - ($b * $percent / 100)));
        
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    public function attendanceView()
    {
        $user = auth()->user();
        
        // Buscar turmas baseado no papel do usuário
        if ($user->hasRole(Role::SUPER_ADMIN->value)) {
            $classes = Classe::with('club')->orderBy('name')->get();
        } elseif ($user->hasAnyRole([
            Role::TREINADOR_GRAU_I->value,
            Role::TREINADOR_GRAU_II->value,
            Role::TREINADOR_GRAU_III->value,
        ])) {
            $trainerClubId = optional($user->profiles[0] ?? null)->club_id;
            $classes = Classe::where('club_id', $trainerClubId)->with('club')->orderBy('name')->get();
        } else {
            $classes = collect();
        }
        
        return view('classes.attendance-view', compact('classes'));
    }

    public function attendanceData(Request $request, $classe)
    {
        $month = $request->input('month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Buscar a turma
        $classe = Classe::with(['students'])->findOrFail($classe);

        // Filtrar aulas baseado no tipo de filtro
        if ($month) {
            // Filtro por mês
            $classe->load(['lessons' => function ($query) use ($month) {
                $query->whereYear('lesson_date', substr($month, 0, 4))
                      ->whereMonth('lesson_date', substr($month, 5, 2))
                      ->orderBy('lesson_date');
            }]);
        } elseif ($startDate && $endDate) {
            // Filtro por período
            $classe->load(['lessons' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('lesson_date', [$startDate, $endDate])
                      ->orderBy('lesson_date');
            }]);
        } else {
            return response()->json(['error' => 'Parâmetros inválidos'], 400);
        }

        $lessons = $classe->lessons;

        // Preparar dados dos alunos
        $studentsData = $classe->students->map(function ($student) use ($lessons) {
            $attendances = [];
            
            foreach ($lessons as $lesson) {
                $attendance = Attendance::where('class_lesson_id', $lesson->id)
                    ->where('student_id', $student->id)
                    ->first();
                
                if ($attendance) {
                    $attendances[] = true;
                } else {
                    $attendances[] = null;
                }
            }

            return [
                'id' => $student->id,
                'name' => $student->name,
                'number_kak' => $student->number_kak,
                'attendances' => $attendances,
            ];
        });

        // Preparar dados das aulas
        $lessonsData = $lessons->map(function ($lesson) {
            return [
                'id' => $lesson->id,
                'lesson_date' => $lesson->lesson_date,
            ];
        });

        return response()->json([
            'students' => $studentsData,
            'lessons' => $lessonsData,
        ]);
    }
}
