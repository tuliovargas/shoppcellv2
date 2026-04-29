<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class DeleteNotAllowedException extends Exception
{
    public function report()
    {
        //
    }

    public function render($request)
    {
        return response()->json([
            'errors' => [
                'message' => 'Não foi possível excluir, registros vinculados',
            ]
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
