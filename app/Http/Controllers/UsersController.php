<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

    public function registro(Request $data)
    {

        $validator_data = [
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ];

        $validacao = Validator::make($validator_data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validacao->fails())
            return $validacao->errors();

        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => bcrypt($data->password),
        ]);
        $user->token = $user->createToken($user->email)
                            ->accessToken;
        return $user;
    }

    public function usuario(Request $request) {
        return $request->user();
    }

    public function login(Request $data){

        $validator_data = [
            'email' => $data->email,
            'password' => $data->password,
        ];

        $validacao = Validator::make($validator_data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if($validacao->fails())
            return $validacao->errors();

        if(auth()->attempt(['email' => $data->email, 'password' => $data->password])){
            $user = auth()->user();
            $user->token = $user->createToken($user->email)
                ->accessToken;
            return $user;
        }

        return response()->json(['error' => 'sem Acesso!'], 200);

    }
}
