<?php

namespace App\Http\Controllers;
use App\Role;
use App\Models\User;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $countStudentsActive = User::role(Role::PRATICANTE)->whereHas('profile', function ($query) {
            $query->where('status', 'active');
        })->count();
        $countStudentsInactive = User::role(Role::PRATICANTE)->whereHas('profile', function ($query) {
            $query->where('status', 'inactive');
        })->count();

        $studentByClub = User::role(Role::PRATICANTE)
            ->whereHas('profile', function ($query) {
                $query->where('status', 'active');
            })
            ->with('profile.club')
            ->get()
            ->groupBy(function ($user) {
                return $user->profile->club ? $user->profile->club->name : 'No Club';
            })
            ->map(function ($group, $clubName) {
                $firstUser = $group->first();
                $club = $firstUser->profile->club;

                return [
                    'name' => $clubName,
                    'logo' => $club && $club->logo ? $club->logo : asset('images/club.png'),
                    'count' => $group->count(),
                ];
            })
            ->values();

        return view('dashboard', compact('countStudentsActive', 'countStudentsInactive', 'studentByClub'));
    }
}
