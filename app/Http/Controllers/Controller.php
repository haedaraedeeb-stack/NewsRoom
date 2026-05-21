<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function successResponse( array $data = null, $message = null, $code = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code,
        ]);
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
