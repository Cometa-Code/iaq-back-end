<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Responses;
use App\Models\User;
use App\Models\YoungApprenticesPresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function get_report(Request $request) {
        $validated = $request->validate([
            'report_type' => 'required',
            'course' => '',
            'course_day' => '',
        ]);

        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $getReport = null;

        if ($validated['report_type'] == 'jovens-por-turma') {
            $getReport = YoungApprenticesPresence::where('course', $validated['course'])
                ->selectRaw('MIN(id) as id, user_id, course')
                ->whereHas('user', function ($query) use ($validated) {
                    $query->whereHas('young_apprentice_data', function ($query) use ($validated) {
                        $query->where('day_theoretical', $validated['course_day']);
                    });
                })
                ->with('user:id,name,email')
                ->groupBy('id', 'user_id', 'course')
                ->get();
        }

        if ($validated['report_type'] == 'jovens-sem-contrato') {
            $getReport = User::where('role', 'youngapprentice')->with('young_apprentice_data:id,user_id,phone_number,education,date_of_birth,address_city,address')->doesntHave('young_apprentice_contracts')->get(['id', 'name', 'email']);
        }

        return Responses::OK('', $getReport);
    }
}
