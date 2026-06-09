<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;
    protected function successResponse( mixed $data = null, $message = null, $code = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code,
        ], $code);
    }

    protected function errorResponse(array $data = null, $message = null, $code = 400)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code,
        ]);
    }


}
