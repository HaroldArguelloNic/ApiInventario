<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Responses\ApiResponse;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        try {
            /* validar los datos */
            $Validator = Validator::make($request->all(), [
                'name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
                'rol_id' => ['required', 'integer'],
                'active' => ['required', 'boolean'],
            ]);

            if ($Validator->fails()) {
                $data = [
                    'message' => 'Error en la validación de los datos',
                    'errors' => $Validator->errors(),
                    'status' => 400,
                ];
                return response()->json($data, 400);
            }


            //$newUser = $request->validated();

            /* Estructurar los datos */
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // encriptar el password
                'rol_id' => $request->rol_id,
                'active' => $request->active,
            ]);

            /* encriptar el password ya validado */
           // $newUser['password'] = Hash::make($newUser['password']);

            /* crear usuario */
           // $user = User::create($newUser);

            /* comprobar si NO se creó exitosamente */
            if (!$newUser) {
                $data = [
                    'message' => 'Registro Fallido',
                    'status' => 500,
                ];
                return response()->json($data, 500,);
            }

            $data = [
                'Usuario' => $newUser,
                'status' => 201,
            ];

            return response()->json($data, 201);

         /*****   $userData = [
                'idUsuario' => $user->id,
                'nombreCompleto' => $user->name,
                'correo' => $user->email,
                'rol_id' => $user->rol_id,
                'rolDescripcion' => $user->rols->name ?? 'Sin rol asignado',
            ];******/

            /* Estructurar la respuesta */
            /***** $data = [
                'user' => $userData,
                // 'token' => $token, // incluir el token (pausado)
            ];****/

            /* devolver los datos */
            //return ApiResponse::Success('Registration Successfully', $data, 201);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function login(Request $request)
    {
        try {

            $loginData = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string', 'min:8'],
            ]);
           $user = User::where('email', $loginData['email'])->first();
           if(!$user){
               return ApiResponse::NotFound('Email Failed',[],200);
           }
           if (!Hash::check($loginData['password'], $user->password)) {
               return ApiResponse::NotFound('Password  Failed',[],200);
           }
            // ✅ Generar token con Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            $role = $user->rols ? $user->rols->name : 'Sin rol asignado';
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rolDescripcion' => $role,
                'token' =>$token, //incluir token en respuesta
            ];
            return ApiResponse::Success('Login Successfully', $userData, 201);

        } catch (\Error $e) {

            return ApiResponse::Error('An error occurred', null, 500);
        }
    }

    public function listUsers() {
        try {

            $users = User::where('active', 1)->with('rols')->get();


            if (!$users) {
                return ApiResponse::Error('Users not found');
            }

            $userList = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rolDescripcion' => $user->rols->name ?? 'No role assigned',
                    'active' => $user->active,
                    'password' => $user->password,
                    'rol_id' => $user->rols->id,
                ];
            });

            return ApiResponse::Success('Users List', $userList, 200);
        } catch (\Exception $e) {
            return ApiResponse::Error($e->getMessage());
        }
    }

    public function profile(Request $request)
    {
        try {
            /* Obtener el token */
            // $token = $request->bearerToken();

            $data = [
                'user' => $request->user(),
                //'token' => $token,
                //'isAdmin' => $request->user()->isAdmin,
            ];

            return ApiResponse::Success('User Profile', $data);
        } catch (\Error $e){
            return ApiResponse::Error($e->getMessage());
        }
    }
}
