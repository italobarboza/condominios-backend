<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class UserController extends Controller
{
    /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User information retrieved successfully.'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'data' => '',
                'message' => 'User not found.'
            ], Response::HTTP_OK);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'There is some erros with this/these field/s.'
            ], Response::HTTP_CONFLICT);
        } else {
            $user = User::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User created successfully.'
            ], Response::HTTP_OK);
        }
    }
}