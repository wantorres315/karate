<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\User;
use App\Models\Arbitrator;
use App\Models\Graduation;
use Carbon\Carbon;
use App\Models\Profile;

class PdfController extends Controller
{
    public function memberPdf(Profile $profile)
    {

        $photoPath = $profile->photo 
            ? public_path('storage/' . $profile->photo) 
            : public_path('images/club.png');

        if (!file_exists($photoPath)) {
            $photoPath = public_path('images/club.png');
        }
        $graduations = Graduation::leftJoin('graduation_users', function ($join) use ($profile) {
                $join->on('graduations.id', '=', 'graduation_users.graduation_id')
                    ->where('graduation_users.profile_id', '=', $profile->id);
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
            'name' => $profile->name,
            'father_name' => $profile->father_name,
            'mother_name' => $profile->mother_name,
            'number_kak' => $profile->number_kak,
            'number_fnkp' => $profile->number_fnkp,
            'cit_number' => $profile->cit_number,
            'tptd_number' => $profile->tptd_number,
            'jks_number' => $profile->jks_number,
            'admission_date' => Carbon::parse($profile->admission_date)->format('d/m/Y'),
            'birth_date' => Carbon::parse($profile->birth_date)->format('d/m/Y'),
            'nationality' => $profile->nationality,
            'profession' => $profile->profession,
            'address' => $profile->address,
            'postal_code' => $profile->postal_code,
            'city' => $profile->city,
            'district' => $profile->district,
            'phone' => $profile->phone_number,
            'cell' => $profile->cell_number,
            'email' => $profile->user->email,
            'photo' => $photoPath,
            'contact_name' => $profile->contact,
            'contact_phone' => $profile->contact_number,
            'contact_cell' => $profile->contact_cell,
            'contact_email' => $profile->contact_email,
            'observations' => $profile->observations,
            'club_name' => $profile->club->name,
            'club_sigla' => $profile->club->acronym,
            'club_city' => $profile->club->club_city,
            "club_location" => $profile->club->city ?? 'N/A',
            'graduation' => $profile->graduations()->latest("date")->first()->graduation->name ?? 'N/A',
            'graduation_color' => $profile->graduations()->latest("date")->first()->graduation->color ?? 'N/A',
            'credits' => $profile->credits,
            "arbitrator" => Arbitrator::find($profile->arbitrator_id)?->name ?? 'N/A',    
            'year' => date('Y'),

            'last_update' => $profile->updated_at ? $profile->updated_at->format('d/m/Y') : 'N/A',
            "member_type" => $profile->user->getRoleNames()->first() ?? 'N/A',
        ];
        //return view('pdf.member', compact('member', 'graduations'));

        $pdf = PDF::loadView('pdf.member', compact('member', 'graduations'));
        return $pdf->stream('member.pdf');
    }
}
