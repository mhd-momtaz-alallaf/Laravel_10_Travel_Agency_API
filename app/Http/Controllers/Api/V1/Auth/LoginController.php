<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    // An invokable controller in Laravel is a controller that has only one action, the __invoke method, // The Benefits of __invoke method is we don't have to specify the method name when we use the controller in the api.php file.
    // To create this controller with the method __invoke we used the command:  php artisan make:controller Api/V1/Auth/LoginController --invokable.

    public function __invoke(Request $request)
    {
        
    }
}
