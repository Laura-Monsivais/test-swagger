<?php

namespace App\Http\Controllers;

use App\Person;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required',
            'firstSurname'  => 'required',
            'secondSurname' => 'sometimes',
            'phone'         => 'required|numeric|digits:10|unique:persons',
            'dateOfBirth'   => 'required|date',
            'gender'        => 'required',
            'user'          => 'required|string|unique:users',
            'email'         => 'required|string|email|unique:users',
            'password'      => 'required|string',
            'rol'           => 'in:Cliente,Administrador'
        ];

        $validation = $this->validateService(Validator::make($request->all(), $rules));

        if (sizeof($validation) == 0) {
            $input['name']          = $request->name;
            $input['firstSurname']  = $request->firstSurname;
            $input['secondSurname'] = $request->secondSurname;
            $input['phone']         = $request->phone;
            $input['dateOfBirth']   = $request->dateOfBirth;
            $input['gender']        = $request->gender;
            $person                 = Person::create($input);

            $user = User::create([
                'person_id' => $person->id,
                'user'      => $request->user,
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password),
                'rol'       => $request->rol,
                'isActive'  => 1
            ]);
            $details = $this->search($user->id);

            $this->response = [
                'message' => 'SUCCESS: User created successfully',
                'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                'result' => [
                    'user_id' => $user->id,
                    'details' => $details
                ]
            ];
            $code = 200;
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

    public function update(Request $request, $id)
    {
        $rules = [
            'name'          => 'required',
            'firstSurname'  => 'required',
            'secondSurname' => 'sometimes',
            'phone'         => 'required|numeric|digits:10|unique:persons,phone,' . $id . ',id',
            'dateOfBirth'   => 'required|date',
            'gender'        => 'required',
            'user'          => 'required|string|unique:users,user,' . $id . ',id',
            'email'         => 'required|string|email|unique:users,email,' . $id . ',id',
            'password'      => 'required|string',
            'rol'           => 'in:Cliente,Administrador',
            'gender'        => 'required',
            'isActive'      => 'required|numeric|in:1,0',
        ];
        $validation = $this->validateService(Validator::make($request->all(), $rules));
        if (sizeof($validation) == 0) {
            $user = User::find($id);
            if ($user != NULL) {
                $person = Person::find($user->person_id);
                $person->name            = $request->name;
                $person->firstSurname    = $request->firstSurname;
                $person->secondSurname   = $request->secondSurname;
                $person->phone           = $request->phone;
                $person->dateOfBirth     = $request->dateOfBirth;
                $person->gender          = $request->gender;
                $person->save();

                $user->user              = $request->user;
                $user->email             = $request->email;
                $user->password          = bcrypt($request->password);
                $user->isActive          = $request->isActive;
                $user->save();

                $details = $this->search($user->id);
                $this->response = [
                    'message' => 'SUCCESS: User updated successfully',
                    'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                    'result'  => [
                        'user_id' => $user->id,
                        'details' => $details
                    ]
                ];
                $code = 200;
            } else {
                $this->response = [
                    'result' => [
                        'message' => 'ERROR',
                        'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                        'details' => "Username doesn't exist"
                    ]
                ];
                $code = 404;
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

    public function delete(Request $request)
    {
        $user = User::find($request->id);
        if ($user != NULL) {
            $person = Person::find($user->person_id);
            $info = $this->search($request->id);
            User::destroy($user->id);
            Person::destroy($person->id);
            $this->response = [
                'message' => 'SUCCESS: User deleted successfully',
                'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                'result' => [
                    'user_id' => $user->id,
                    'details' => $info
                ]
            ];
            $code = 200;
        } else {
            $this->response = [
                'result' => [
                    'message' => 'ERROR',
                    'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                    'details' => "Username doesn't exist"
                ]
            ];
            $code = 404;
        }
        return response()->json($this->response, $code);
    }
    
    public function show($user_id)
    {
        $user = User::find($user_id);
        if ($user != NULL) {
            $info = $this->search($user_id);
            $this->response = [
                'message' => 'SUCCESS: User information',
                'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                'result' => [
                    'user_id' => $user_id, 'details' => $info
                ]
            ];
            $code = 200;
        } else {
            $this->response = [
                'result' => [
                    'message' => 'ERROR',
                    'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                    'details' => "Username doesn't exist"
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
