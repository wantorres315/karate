<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\File; 
use App\Http\Controllers\GraduationController;
use App\Http\Controllers\ClubController;


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


Route::middleware(['auth', 'verified'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('student')->controller(StudentController::class)->group(function () {
        Route::get('/', 'index')->name('student.index');
        Route::get('/create', 'create')->name('student.create');
        Route::post('/', 'store')->name('student.store');
        Route::get('/{profile}/edit', 'edit')->name('student.edit');
        Route::put('/{profile}', 'update')->name('student.update');
        Route::delete('/{id}', 'destroy')->name('student.destroy');
        Route::get('/{profile}/graduations', 'graduations')->name('student.graduations');
        Route::post('/{profile}/graduations', 'addGraduation')->name('student.addGraduation');
        Route::delete('/{profile}/graduations/{graduationUser}', 'removeGraduation')->name('student.removeGraduation');
    });

    Route::resource('graduations', GraduationController::class);
    Route::resource('clubs', ClubController::class);

    Route::post('/check-email', [StudentController::class, 'checkEmail'])->name('check-email');
    Route::get('/member-pdf/{profile}', [PdfController::class, 'memberPdf'])->name('member.pdf');

});

require __DIR__.'/auth.php';
