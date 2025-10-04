<?php

namespace App\Http\Controllers;
use App\Role;
use App\Models\Profile;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if(auth()->user()->hasRole(Role::SUPER_ADMIN)){
            $countStudentsActive =  Profile::with('user', 'club')
            ->whereHas('user', function($q) {
                $q->role(Role::PRATICANTE)->visibleStudents();
            })
            ->count();

            $countStudentsInactive =  Profile::with('user', 'club')
                ->whereHas('user', function($q) {
                    $q->role(Role::PRATICANTE)
                    ->visibleStudents();
                })
                ->where('status', 'inactive')
                ->count();

            $studentByClub = Profile::with('user', 'club')
            ->whereHas('user', function($q) {
                $q->role(Role::PRATICANTE)
                ->visibleStudents(); // aqui funciona porque está no user
            })
            ->where('status', 'active')
            ->get()
            ->groupBy(function ($profile) {
                return $profile->club?->name ?? 'No Club';
            })
            ->map(function ($group, $clubName) {
                $firstProfile = $group->first();
                $club = $firstProfile->club;

                return [
                    'name'  => $clubName,
                    'logo'  => $club?->logo ?: asset('images/club.png'),
                    'count' => $group->count(),
                ];
            })
            ->values();

            return view('dashboard', compact(
                'countStudentsActive',
                'countStudentsInactive',
                'studentByClub'
            ));
        }else{
            return view("dashboard_user");
        }
        
    }


}
