<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Responses;
use App\Models\User;
use App\Models\YoungApprenticesPresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YoungApprenticesPresenceController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'course' => 'required',
            'date' => 'required',
            'presence' => 'required',
        ]);

        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $getUser = User::where('id', $validated['user_id'])->first();

        if (!$getUser) {
            return Responses::NOTFOUND('Usuário não encontrado!');
        }

        $create = YoungApprenticesPresence::create($validated);

        if (!$create) {
            return Responses::BADREQUEST('Ocorreu um erro ao criar!');
        }

        return Responses::CREATED('Criado com sucesso!', $create);
    }

    public function show(Request $request, $userId)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $itemsPerPage = $request->query('items_per_page', 10);

        $getPresences = YoungApprenticesPresence::where('user_id', $userId)->with('user:id,name')->orderBy('date', 'DESC')->paginate($itemsPerPage);

        return Responses::OK('', $getPresences);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role != 'admin' && $user->role != 'superadmin') {
            return Responses::BADREQUEST('Apenas usuários permitidos podem executar essa ação!');
        }

        $getPresence = YoungApprenticesPresence::where('id', $id)->first();

        if (!$getPresence) {
            return Responses::BADREQUEST('Presença não localizada!');
        }

        $getPresence->delete();

        return Responses::OK('Presença removida com sucesso!');
    }
}
