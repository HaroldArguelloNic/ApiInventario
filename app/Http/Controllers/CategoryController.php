<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Responses\ApiResponse;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function index()
    {
        try{
            $categories = Category::all();
            if(!$categories){
                return ApiResponse::NotFound('Category Not Found');
            }
            return ApiResponse::Success('Category List',$categories);
        }
        catch(\Exception $e){
            return ApiResponse::Error($e->getMessage());
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'name' => ['required','string','max:40'],
                'status' => ['required','boolean'],
            ]);
            if($valid->fails()){
                $data=[
                    'message'=>'Error en la validacion de los datos',
                    'errors' => $valid->errors(),
                    'status' => 400,
                ];
                return response()->json($data, 400);
            }
            $category= Category::create([
                'name'=> $request->name,
                'status' => $request->status,
            ]);

            if(!$category){
                return ApiResponse::Error('Error al Registrar');
            }
            return ApiResponse::Success('Category Registered' ,$category);

        }catch (\Exception $e){
            return ApiResponse::Error($e->getMessage());
        }
    }
    public function show($id) {
        try{
            $categories = Category::find($id);
            if(!$categories){
                return ApiResponse::NotFound('Category Not Found');
            }
            $data= [
                'id'=>$categories->id,
                'name'=>$categories->name,
                'status'=>$categories->status
            ];
            return ApiResponse::Success('Categoria registrada',$data, 200);
        }
        catch(\Exception $e){
            return ApiResponse::Error($e->getMessage());
        }
    }
    //Actualizar categoria
    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:40'],
                'status' => ['required', 'integer'],
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos',
                    'errors' => $valid->errors(),
                    'status' => 400,
                ], 400);
            }

            $category = Category::find($id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Categoría no encontrada'
                ], 404);
            }

            $validated = $valid->validated();
            $category->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Categoría actualizada',
                'value' => $category
            ]);
        } catch (\Exception $e) {
            return ApiResponse::Error($e->getMessage());
        }
    }

    public function destroy($id) {
        try {

            $category = Category::find($id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Categoría no encontrada'
                ], 404);
            }

            $category->status = false;
            $category->save();
            $data=[
                'category' =>$category
            ];
            return response()->json($data, 200);


        } catch (\Exception $e) {
            return ApiResponse::Error($e->getMessage());
        }
    }

}
