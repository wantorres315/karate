<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());
        

        // Se email mudou, desmarca verificação
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Trata foto em base64
        if ($request->filled('photo_data')) {
            try {
                $photoData = $request->input('photo_data');

                if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                    $photoData = substr($photoData, strpos($photoData, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                        return Redirect::back()->withErrors(['photo_data' => 'Formato de imagem inválido.']);
                    }

                    $photoData = base64_decode($photoData);

                    if ($photoData === false) {
                        return Redirect::back()->withErrors(['photo_data' => 'Erro ao decodificar imagem.']);
                    }

                    // Garante que o diretório existe
                    $directory = 'profile_photos';
                    if (!Storage::exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory, 0755, true);
                    }
                    
                    $fileName = 'profile_' . $user->id . '_' . time() . '.' . $type;
                    $filePath = $directory . '/' . $fileName;
                    
                    // Salva usando o disco 'public' e o método put()
                    if (Storage::disk('public')->put($filePath, $photoData)) {
                        // Remove a foto antiga se existir
                        if ($user->photo) {
                            $oldPhotoPath = str_replace('/storage/', 'public/', $user->photo);
                            if (Storage::disk('public')->exists($oldPhotoPath)) {
                                Storage::disk('public')->delete($oldPhotoPath);
                            }
                        }
                        // Salva o caminho relativo para a nova foto
                        $user->photo = '/storage/profile_photos/' . $fileName;
                    } else {
                        throw new \Exception('Falha ao salvar a imagem.');
                    }
                } else {
                    return Redirect::back()->withErrors(['photo_data' => 'Formato de imagem inválido.']);
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao salvar a foto de perfil: ' . $e->getMessage());
                return Redirect::back()->withErrors(['photo_data' => 'Erro ao processar a imagem. Por favor, tente novamente.']);
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
