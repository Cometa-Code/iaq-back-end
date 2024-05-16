<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Responses;
use App\Models\CompanyData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompaniesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $itemsPerPage = $request->query('items_per_page', 10);
        $termsFilter = $request->query('terms_filter', '');

        $listCompanies = User::where(function($query) use ($termsFilter) {
            $query->where('name', 'LIKE', "%$termsFilter%")
                ->orWhere('email', 'LIKE', "%$termsFilter%")
                ->orWhere('principal_document', 'LIKE', "%$termsFilter%");
            })
            ->where('role', '=', 'company')
            ->with('company_data')
            ->orderBy('id', 'DESC')
            ->paginate($itemsPerPage);

        return Responses::OK('', $listCompanies);
    }
}
