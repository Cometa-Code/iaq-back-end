<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Responses;
use App\Http\Requests\Contracts\CreateContractRequest;
use App\Models\Cbos;
use App\Models\Contracts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractsController extends Controller
{
    public function store(CreateContractRequest $request)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $getYoungApprentice = User::where('id', $request->young_apprentice_id)->first();

        if (!$getYoungApprentice) {
            return Responses::NOTFOUND('Não foi possível encontrar esse jovem aprendiz!');
        }

        $getCompany = User::where('id', $request->company_id)->first();

        if (!$getCompany) {
            return Responses::NOTFOUND('Não foi possível encontrar essa empresa!');
        }

        $getCbo = Cbos::where('id', $request->cbo_id)->first();

        if (!$getCbo) {
            return Responses::NOTFOUND('Não foi possível encontrar esse CBO!');
        }

        $data = $request->all();

        $createContract = Contracts::create($data);

        if (!$createContract) {
            return Responses::BADREQUEST('Ocorreu um erro durante a criação do contrato!');
        }

        return Responses::CREATED('Contrato criado com sucesso!');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $itemsPerPage = $request->query('items_per_page', 10);
        $termsFilter = $request->query('terms_filter', '');

        $listContracts = Contracts::where('contract_number', 'LIKE', "%$termsFilter%")
                                    ->with('young_apprentice')
                                    ->with('company')
                                    ->with('cbo')
                                    ->paginate($itemsPerPage);

        return Responses::OK('', $listContracts);
    }

    public function show($id)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $contract = Contracts::where('id', $id)
                                    ->with('young_apprentice')
                                    ->with('company')
                                    ->with('cbo')
                                    ->first();

        if (!$contract) {
            return Responses::NOTFOUND('Não foi possível encontrar o contrato especificado!');
        }

        return Responses::OK('', $contract);
    }
}
