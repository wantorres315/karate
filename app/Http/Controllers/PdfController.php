<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\User;
use App\Models\Arbitrator;
use App\Models\Graduation;
use Carbon\Carbon;

class PdfController extends Controller
{
    public function memberPdf(User $user)
    {

        $photoPath = $user->profile->photo 
            ? public_path('storage/' . $user->profile->photo) 
            : public_path('images/club.png');

        if (!file_exists($photoPath)) {
            $photoPath = public_path('images/club.png');
        }
        $graduations = Graduation::leftJoin('graduation_users', function ($join) use ($user) {
                $join->on('graduations.id', '=', 'graduation_users.graduation_id')
                    ->where('graduation_users.user_id', '=', $user->id);
            })
            ->select(
                'graduations.id',
                'graduations.name',
                'graduations.color',
                'graduation_users.date'
            )
            ->orderBy('graduations.id')
            ->get();
        // Dados vazios por enquanto
        $member = [
            'name' => $user->name,
            'father_name' => $user->profile->father_name,
            'mother_name' => $user->profile->mother_name,
            'number_kak' => $user->profile->number_kak,
            'number_fnkp' => $user->profile->number_fnkp,
            'cit_number' => $user->profile->cit_number,
            'tptd_number' => $user->profile->tptd_number,
            'jks_number' => $user->profile->jks_number,
            'admission_date' => Carbon::parse($user->profile->admission_date)->format('d/m/Y'),
            'birth_date' => Carbon::parse($user->profile->birth_date)->format('d/m/Y'),
            'nationality' => $user->profile->nationality,
            'profession' => $user->profile->profession,
            'address' => $user->profile->address,
            'postal_code' => $user->profile->postal_code,
            'city' => $user->profile->city,
            'district' => $user->profile->district,
            'phone' => $user->profile->phone_number,
            'cell' => $user->profile->cell_number,
            'email' => $user->email,
            'photo' => $photoPath,
            'contact_name' => $user->profile->contact,
            'contact_phone' => $user->profile->contact_number,
            'contact_cell' => $user->profile->contact_cell,
            'contact_email' => $user->profile->contact_email,
            'observations' => $user->profile->observations,
            'club_name' => $user->profile->club->name,
            'club_sigla' => $user->profile->club->acronym,
            'club_city' => $user->profile->club->club_city,
            "club_location" => $user->profile->club->city ?? 'N/A',
            'graduation' => $user->graduations()->latest("date")->first()->graduation->name ?? 'N/A',
            'graduation_color' => $user->graduations()->latest("date")->first()->graduation->color ?? 'N/A',
            'credits' => $user->profile->credits,
            "arbitrator" => Arbitrator::find($user->profile->arbitrator_id)?->name ?? 'N/A',    
            'year' => date('Y'),

            'last_update' => $user->profile->updated_at ? $user->profile->updated_at->format('d/m/Y') : 'N/A',
            "member_type" => $user->getRoleNames()->first() ?? 'N/A',
        ];
        //return view('pdf.member', compact('member', 'graduations'));

        $pdf = PDF::loadView('pdf.member', compact('member', 'graduations'));
        return $pdf->stream('member.pdf');
    }
}
