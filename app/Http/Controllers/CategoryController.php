<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Responses\ApiResponse;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
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

}
