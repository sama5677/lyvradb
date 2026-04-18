<?php

namespace App\Helpers;


class ResponseHelper
{
    public static function success($message, $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($message, $error = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error
        ], $code);
    }
}