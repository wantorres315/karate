<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\File; 
use App\Http\Controllers\GraduationController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\FamilyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/offline', 'offline');


Route::get('/pwa-assets', function () {
    $manifestPath = public_path('build/manifest.json');
    if (!File::exists($manifestPath)) {
        return response()->json([]);
    }

    $manifest = json_decode(File::get($manifestPath), true);
    $files = [];

    foreach ($manifest as $entry) {
        if (isset($entry['file'])) {
            $files[] = '/build/'.$entry['file'];
        }
        if (isset($entry['css'])) {
            foreach ($entry['css'] as $css) {
                $files[] = '/build/'.$css;
            }
        }
    }

    // Sempre incluir rota offline
    $files[] = '/offline';

    return response()->json($files);
});


Route::middleware(['auth'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

   Route::prefix('members')->controller(MembersController::class)->group(function () {
        Route::get('/', 'index')->name('members.index');
        Route::get('/create', 'create')->name('members.create');
        Route::post('/', 'store')->name('members.store');
        Route::get('/{profile}/edit', 'edit')->name('members.edit');
        Route::put('/{profile}', 'update')->name('members.update');
        Route::delete('/{profile}', 'destroy')->name('members.destroy');
        Route::get('/{profile}/graduations', 'graduations')->name('members.graduations');
        Route::post('/{profile}/graduations', 'addGraduation')->name('members.addGraduation');
        Route::delete('/{profile}/graduations/{graduationUser}', 'removeGraduation')->name('members.removeGraduation');
        Route::post('/{profile}/toggle-treinador','toggleTreinador')->name('members.toggle-treinador');
    });

    Route::prefix("classes")->group(function(){
        Route::controller(ClasseController::class)->group(function(){
            Route::get("/", "index")->name("classes.index");
            Route::get('/create', 'create')->name('classes.create');
            Route::post('/', 'store')->name('classes.store');
            Route::get('/{classe}/edit', 'edit')->name('classes.edit');
            Route::put('/{classe}', 'update')->name('classes.update');
            Route::delete('/{classe}', 'destroy')->name('classes.destroy');
            Route::get('/{classe}/attendance', 'attendance')->name('classes.attendance');
            Route::post('/{classe}/attendance', 'saveAttendance')->name('classes.saveAttendance');    
        });
        Route::controller(PdfController::class)->group(function(){
            Route::get("/pdf/{classe}", "classesPDF")->name("classes.pdf");
         });   
        
    });
    Route::prefix("clubs")->group(function(){
        Route::get('/{club}/members', [ClubController::class, 'members']);
        Route::get('/{club}/students', [ClasseController::class, 'getStudents'])->name('clubs.students');
        Route::get("/{club}/addCoach", [ClubController::class, "addCoach"] )->name("clubs.addCoach");
        Route::post('/{club}/store-coach', [ClubController::class, 'storeCoach'])->name('clubs.storeCoach');
        Route::delete('/{club}/remove-coach/{profile}', [ClubController::class, 'removeCoach'])->name('clubs.removeCoach');
    });


    Route::prefix("schedule")->controller(ScheduleController::class)->group(function(){
        Route::get("/", "index")->name("schedule.index");
    });
    Route::resource('graduations', GraduationController::class);
    Route::resource('clubs', ClubController::class);
    
    
    Route::prefix("/events")->controller(AgendaController::class)->group(function(){
        Route::get('/',  'index');
        Route::post('/', 'store');
        Route::put('/{event}','update');
        Route::delete('/{event}', 'destroy');
        Route::post('/export-pdf', 'exportPdf')->name('events.exportPdf');
    });

    Route::post('/check-email', [StudentController::class, 'checkEmail'])->name('check-email');
        Route::get('/member-pdf/{profile}', [PdfController::class, 'memberPdf'])->name('member.pdf');
        Route::prefix('boletos')->name('boletos.')->group(function () {
        Route::get('/', [BoletoController::class, 'index'])->name('index');
        Route::post('/gerar', [BoletoController::class, 'gerar'])->name('gerar');
        Route::post('/{boleto}/pagar', [BoletoController::class, 'marcarPago'])->name('pagar');
        Route::post('/{boleto}/comprovante', [BoletoController::class, 'uploadComprovante'])->name('comprovante');
        Route::get('/{boleto}/download', [BoletoController::class, 'downloadBoleto'])->name('download');
        Route::get('/{boleto}/comprovante/download', [BoletoController::class, 'downloadComprovante'])->name('comprovante.download');
    });

    Route::prefix('familias')->controller(FamilyController::class)->group(function () {
        Route::get('/', 'index')->name('familias.index');
        Route::get('/create', 'create')->name('familias.create');
        Route::post('/', 'store')->name('familias.store');
        Route::get('/{family}/edit', 'edit')->name('familias.edit');
        Route::put('/{family}', 'update')->name('familias.update');
        Route::delete('/{family}', 'destroy')->name('familias.destroy');
        Route::get('/{family}/members', 'members')->name('familias.members');
        Route::get('/{family}/students', 'getStudents')->name('familias.students');
        Route::get('/{family}/addCoach', 'addCoach')->name('familias.addCoach');
        Route::post('/{family}/store-coach', 'storeCoach')->name('familias.storeCoach');
        Route::delete('/{family}/remove-coach/{profile}', 'removeCoach')->name('familias.removeCoach');
        Route::get('/{family}/graduations', 'graduations')->name('familias.graduations');
        Route::post('/{family}/graduations', 'addGraduation')->name('familias.addGraduation');
        Route::delete('/{family}/graduations/{graduationUser}', 'removeGraduation')->name('familias.removeGraduation');
        Route::post('/{family}/toggle-treinador', 'toggleTreinador')->name('familias.toggle-treinador');
        Route::get('/{family}/attendance', 'attendance')->name('familias.attendance');
        Route::post('/{family}/attendance', 'saveAttendance')->name('familias.saveAttendance');
        Route::get('/{family}/pdf', 'memberPdf')->name('familias.pdf');
        Route::post('/{family}/reset-senha',  'resetSenha')->name('familias.resetSenha');

        Route::prefix('boletos')->name('familias.boletos.')->group(function () {
            Route::get('/', [BoletoController::class, 'index'])->name('index');
            Route::post('/gerar', [BoletoController::class, 'gerar'])->name('gerar');
            Route::post('/{boleto}/pagar', [BoletoController::class, 'marcarPago'])->name('pagar');
            Route::post('/{boleto}/comprovante', [BoletoController::class, 'uploadComprovante'])->name('comprovante');
            Route::get('/{boleto}/download', [BoletoController::class, 'downloadBoleto'])->name('download');
            Route::get('/{boleto}/comprovante/download', [BoletoController::class, 'downloadComprovante'])->name('comprovante.download');
        });
    });


});

    require __DIR__.'/auth.php';
