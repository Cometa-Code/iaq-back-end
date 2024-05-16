<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Responses;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\CompanyData;
use App\Models\User;
use App\Models\YoungApprenticeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        if ($request->type != 'youngapprentice' && $request->type != 'company' && $request->type != 'jasd87h2jhas7dj12jk38sdhj') {
            return Responses::BADREQUEST('Tipo de criação inválido.');
        }

        if ($request->type == 'youngapprentice' && !$request->document_cpf) {
            return Responses::BADREQUEST('Não foi possível encontrar o campo "document_cpf" para esse tipo de criação.');
        }

        if ($request->type == 'company' && !$request->cnpj_company) {
            return Responses::BADREQUEST('Não foi possível encontrar o campo "cnpj_company" para esse tipo de criação.');
        }

        $email = ($request->type == 'youngapprentice') ? $request->email : $request->email_youth_supervisor;
        $document = ($request->type == 'youngapprentice') ? $request->document_cpf : $request->cnpj_company;

        $getUser = User::where('email', $email)
                        ->orWhere('principal_document', $document)
                        ->first();

        if ($getUser) {
            return Responses::BADREQUEST('Usuário já cadastrado com os dados informados!');
        }

        $name = ($request->type == 'youngapprentice') ? $request->name : $request->name_youth_supervisor;
        $role = ($request->type == 'youngapprentice') ? 'youngapprentice' : 'company';
        $is_active = 1;

        $userData = [
            "name" => $name,
            "email" => $email,
            "principal_document" => $document,
            "role" => $role,
            "is_active" => $is_active,
            "password" => $request->password
        ];

        $createUser = User::create($userData);

        if (!$createUser) {
            return Responses::BADREQUEST('Ocorreu um erro ao criar o usuário.');
        }

        if ($request->type == 'youngapprentice') {
            $data = $request->all();
            $data['user_id'] = $createUser->id;

            $createYoungData = YoungApprenticeData::create($data);

            if (!$createYoungData) {
                return Responses::BADREQUEST('Erro ao adicionar informações sobre o jovem.');
            }
        }

        if ($request->type == 'company') {
            $data = $request->all();
            $data['user_id'] = $createUser->id;

            $createCompany = CompanyData::create($data);

            if (!$createCompany) {
                return Responses::BADREQUEST('Erro ao adicionar informações sobre a empresa.');
            }
        }

        $token = $createUser->createToken('auth_token')->plainTextToken;

        return Responses::CREATED('Usuário criado com sucesso!', $token);
    }

    public function user()
    {
        $user = Auth::user();

        if ($user->role == 'company') {
            $user->company_data;
        }

        if ($user->role == 'youngapprentice') {
            $user->young_apprentice_data;
        }

        return $user;
    }
}
