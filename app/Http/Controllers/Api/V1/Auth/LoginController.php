<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller // To create this controller with the method __invoke we used the command:  php artisan make:controller Api/V1/Auth/LoginController --invokable.
{
    // An invokable controller in Laravel is a controller that has only one action, the __invoke method, // The Benefits of __invoke method is we don't have to specify the method name when we use the controller in the api.php file.
    public function __invoke(LoginRequest $request)
    {
        // Find the user by email
        $user = User::where("email", $request->email)->first();

        // Check if the user exists and if the provided password matches the stored password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'The Provided Credentials is Invalid',
            ], 422); // 422 validation error code.
        }

        // Extract and Truncate User Agent String to string with maximum length of 255 characters
        $device = substr($request->userAgent() ?? '', 0, 255); 

        // Create a new personal access token for the user and return it as part of the response
        return response()->json([
            'access_token' => $user->createToken($device)->plainTextToken,
        ]);
    }

    /*
        $device = substr($request->userAgent() ?? '', 0, 255);

        $request->userAgent() retrieves the user agent string from the HTTP request headers.

            1- $request->userAgent() ?? '': This part fetches the user agent string from the request. If the user agent string is null, it defaults to an empty string ('').

            2- 0: This is the starting position for the substring. It means the function will start extracting from the first character of the string.
            
            3- 255: This is the length of the substring. It means the function will extract up to 255 characters starting from the beginning.


        substr($request->userAgent() ?? '', 0, 255) truncates the user agent string to a maximum length of 255 characters. This is useful to prevent excessively long strings from causing issues when stored or processed.

        Example: User Agent String Shorter than 255 Characters:
            userAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36"
            
            The length of this string is 106 characters.

            when Using substr($request->userAgent() ?? '', 0, 255):
            $device = substr("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36", 0, 255);
            
            // $device will be the same as the input string since its length is less than 255 characters.

            $device = Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36

            The Token will be generated for that specific Agent ($device).
    */
}
