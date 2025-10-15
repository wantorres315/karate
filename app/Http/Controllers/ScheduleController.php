<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\ClubInstructors;
use App\Role;

class ScheduleController extends Controller
{
    /**
     * Listar clubes com filtros
     */
    public function index(Request $request)
    {
        return view('schedule.index');
    }

}
