<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required|string|email',
            'password' => 'required|string'
        ];
        $validation = $this->validateService(Validator::make($request->all(), $rules));
        if (sizeof($validation) == 0) {
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                $this->response = [
                    'result' => [
                        'message' => 'ERROR: Incorrect credentials',
                        'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                        'details' => ''
                    ]
                ];
                $code = 404;
            } else {
                $user        = $request->user();
                $tokenResult = $user->createToken('Personal Access Token');
                $token       = $tokenResult->token;
                $details     = $this->search($user->id);
                $this->response = [
                    'message' => 'SUCCESS: Successful login',
                    'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                    'result'  => [
                        'user_id'      => $user->id,
                        'access_token' => $tokenResult->accessToken,
                        'token_type'   => 'Bearer',
                        'expires_at'   => Carbon::parse($token->expires_at)->toDateTimeString(),
                        'details'      => $details
                    ]
                ];
                $code = 200;
            }
        } else {
            $this->response = [
                'result' => [
                    'message' => 'ERROR',
                    'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                    'details' => [$validation]
                ]
            ];
            $code = 404;
        }
        return response()->json($this->response, $code);
    }

    public function search($userId)
    {
        $user = User::find($userId);
        $details = User::join('persons', 'persons.id', 'users.person_id')->where('person_id', $user->person_id)->first();
        return [
            'user'        => $details->user,
            'email'       => $details->email,
            'rol'         => $details->rol,
            'phone'       => $details->phone,
            'name'        => $details->name,
            'gender'      => $details->gender,
            'dateOfBirth' => $details->dateOfBirth,
        ];
    }

    function validateService($validator)
    {
        if ($validator->passes()) {
            $validation = $validator->validated();
        }
        $validation = $validator->errors()->all();
        return $validation;
    }
}
