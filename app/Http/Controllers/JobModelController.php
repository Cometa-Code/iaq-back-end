<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Responses;
use App\Models\JobModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobModelController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'contact' => 'required|string',
        ]);

        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $getCompany = User::where('id', $request->company_id)->first();

        if (!$getCompany) {
            return Responses::NOTFOUND('Não foi possível encontrar essa empresa!');
        }

        $createJob = JobModel::create([
            'user_id' => $getCompany->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'number_of_vacancies' => 1,
            'contact' => $validated['contact']
        ]);

        if (!$createJob) {
            return Responses::BADREQUEST('Ocorreu um erro durante a criação da vaga!');
        }

        return Responses::CREATED('Vaga adicionada com sucesso!');
    }

    public function index()
    {
        $getJobs = JobModel::with('user:id,name')->orderBy('id', 'DESC')->get();

        if (!$getJobs) {
            return Responses::BADREQUEST('Não foi possível encontrar jobs');
        }

        return $getJobs;
    }

    public function index_admin(Request $request)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $itemsPerPage = $request->query('items_per_page', 10);

        $getJobs = JobModel::with('user.company_data')->orderBy('id', 'DESC')->paginate($itemsPerPage);

        return Responses::OK('', $getJobs);
    }

    public function show($id)
    {
        $getJob = JobModel::where('id', $id)->with('user:id,name')->first();

        if (!$getJob) {
            return Responses::BADREQUEST('Não foi possível encontrar esse job');
        }

        return $getJob;
    }

    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $getJob = JobModel::where('id', $id)->with('user:id,name')->first();

        if (!$getJob) {
            return Responses::NOTFOUND('Não foi possível encontrar essa vaga!');
        }

        $getJob->delete();

        return Responses::OK('Vaga removida com sucesso!');
    }
}
