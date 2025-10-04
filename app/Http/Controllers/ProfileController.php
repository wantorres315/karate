<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
       
        // Campos que estão na tabela users
        $user->fill($request->only(['name','email']));
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        return redirect()->route('profile.edit')->with('status','profile-updated');
    }

}
