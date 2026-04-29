<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;

class PasswordVerifyController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        if (!isset($data['password']) && !isset($data['oldPassword'])) {
            return abort(500);
        }
        if (isset($data['password'])) {
            $password = $data['password'];
            $column = 'secret_word';
        } else {
            $password = $data['oldPassword'];
            $column = 'secret_word_for_old_orders';
        }
        $secretPass = Configuration::where('key', $column)->first();

        if (strval($password) === strval($secretPass->value)) {
            return response()->json([
                'message' => 'Senha mestra verificada e ação permitida'
            ], 200);
        }

        return response()->json([
            'errors' => [
                'Senha mestra incorreta'
            ]
        ], 401);
    }
}
