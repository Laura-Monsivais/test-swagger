<?php

namespace App\Http\Controllers;

use App\Music;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MusicController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required',
            'author'        => 'required',
        ];
        $validation = $this->validateService(Validator::make($request->all(), $rules));

        if (sizeof($validation) == 0) {
            $music = Music::create([
                'person_id' => Auth::user()->person_id,
                'name'      => $request->name,
                'author'    => $request->author,
            ]);
            $this->response = [
                'message' => 'SUCCESS: Music created successfully',
                'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                'result' => [
                    'details' => $music
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
            'author'        => 'required',
        ];
        $validation = $this->validateService(Validator::make($request->all(), $rules));
        if (sizeof($validation) == 0) {
            $music = Music::find($id);
            if ($music != NULL) {
                $music->update([
                    'name'      => $request->name,
                    'author'    => $request->author,
                ]);
                $this->response = [
                    'message' => 'SUCCESS: Music updated successfully',
                    'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                    'result'  => [
                        'details' => $music
                    ]
                ];
                $code = 200;
            } else {
                $this->response = [
                    'result' => [
                        'message' => 'ERROR',
                        'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                        'details' => "Music doesn't exist"
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
        $music = Music::find($request->id);
        if ($music != NULL) {
            Music::destroy($request->id);
            $this->response = [
                'message' => 'SUCCESS: Music deleted successfully',
                'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                'result' => [
                    'details' => $music
                ]
            ];
            $code = 200;
        } else {
            $this->response = [
                'result' => [
                    'message' => 'ERROR',
                    'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                    'details' => "Music doesn't exist"
                ]
            ];
            $code = 404;
        }
        return response()->json($this->response, $code);
    }

    public function show($user_id)
    {
        $user = User::find($user_id);;
        if ($user != NULL) {
            $user           = User::join('persons', 'persons.id', 'users.person_id')->where('person_id', $user->person_id)->first();
            $musical_tastes = Music::where('person_id', $user->person_id)->get();

            $this->response = [
                'message' => 'SUCCESS: Information on the users musical tastes',
                'folio'   => Carbon::now()->timestamp . " -> " . Carbon::now()->toDateTimeString(),
                'result' =>  [
                    'user_id' => $user->id,
                    'details' => [
                        'user'        => $user->user,
                        'email'       => $user->email,
                        'rol'         => $user->rol,
                        'phone'       => $user->phone,
                        'name'        => $user->name,
                        'gender'      => $user->gender,
                        'dateOfBirth' => $user->dateOfBirth
                    ],
                    'musical_tastes'  => $musical_tastes
                ]];
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

    function validateService($validator)
    {
        if ($validator->passes()) {
            $validation = $validator->validated();
        }
        $validation = $validator->errors()->all();
        return $validation;
    }
}
