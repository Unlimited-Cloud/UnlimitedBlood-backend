<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class AuthenticationController extends Controller
{
    public function sendSms(): JsonResponse
    {
        $countryCode = "977";
        $phone = "9843737985";
        $message = "Hello World";

    }


}
