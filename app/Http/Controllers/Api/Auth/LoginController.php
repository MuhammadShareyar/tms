<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ResponseHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoginUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @OA\POST(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Auth"},
     *      summary="Login",
     * 
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email","password"},
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="Email",
     *                     type="string",
     *                     
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Password",
     *                     type="string",
     *                     
     *                 ),
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          ),
     *      @OA\Response(
     *          response=404,
     *          description="Page Not Found"
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *          )
     *      )
     */
    public function login(Request $request)
    {
        $this->validate($request);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => "Invalid credentials",
            ]);
        }

        $user->tokens()->delete();

        $responseData = new LoginUserResource($user);

        return ResponseHandler::success($responseData, 'User logged in successfully');
    }

    public function validate(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    }
}
