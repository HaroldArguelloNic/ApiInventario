<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Responses\ApiResponse;
use App\Http\Requests\StoreProductsRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::where('active', 1)->get();
            if (!$products) {
                return ApiResponse::NotFound('Products not found', [], 404);
            }

            //aqui devuelve el producto con mas detalles como la categoria y todo ese rollo
            $data = $products->map(function ($product) {
                $descripcion = $product->categories ? $product->categories->name : "none";
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_path' => $product->image_path ? asset('storage/' . $product->image_path) : null,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category_id' => $product->category_id,
                    'description' => $product->description,
                    'active' => $product->active,
                ];
            });
            return ApiResponse::Success('isSuccess', $data,200);

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function store(StoreProductsRequest $request)
    {
        try{
            $Validator = Validator::make($request->all(), [
                'name'=> ['required','string'],
                'description' => ['required','string'],
                'price'=> ['required','numeric'],
                'stock'=> ['required','integer'],
                'image_path'=> ['required','string'],
                'category_id'=> ['required','integer'],
                'active'=> ['required','boolean'],
            ]);

            //$validator= request()->validated();
            if($Validator->fails()){
                $data=[
                    'message'=>'Error en la validacion de los datos',
                    'errors' => $Validator->errors(),
                    'status' => 400,
                ];
                return response()->json($data, 400);
            }
            /* Estructurar los datos */
                $newProduct = Product::create([
               'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'image_path' => $request->image_path,
                'active' => $request->active,
                'category_id' => $request->category_id,
           ]);


            if(!$newProduct){
               $data=[
                     'message'=>'Error en la creacion del producto',
                     'status'=>500,
                ];
                return response()->json($data,500);
            }
            $data=[
                'producto'=>$newProduct,
                'status'=>201,
            ];
            return response()->json($data,201);
        }
        catch (\Exception $exception){
            return ApiResponse::error('Error',$exception->getMessage(),500);
        }

    }

    public function show(string $id)
    {
        //aunque en angular se puede filtrar
       try{
           $products = Product::find($id);

           if (!$products) {
               return ApiResponse::NotFound('Product not found', [], 404);
           }
          // $descripcion = $products->categories ? $products->categories->name : "none";

           $data = [
               'id' => $products->id,
               'name' => $products->name,
               'price' => $products->price,
               'stock' => $products->stock,
               'category_id' => $products->category_id,
               'description' => $products->description,
               'active' => $products->active,
               'image_path' => $products->image_path,
           ];

           return ApiResponse::Success('isSuccess', $data, 200);

       }catch (\Exception $exception){
           return $exception->getMessage();
       }
    }
    //funcion de busqueda de producto
    public function search(Request $request)
    {
        $query = Product::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        return response()->json(['product' => $query->get()]);
    }

    // Actualizar un producto existente
    public function update(Request $request, $id)
    {
        // Validar datos entrantes
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'image_path' => 'nullable|string', // o validar formato si deseas
            'active' => 'required|boolean',
        ]);
        // Buscar producto por id
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }
        // Actualizar datos del producto
        $product->name = $validatedData['name'];
        $product->description = $validatedData['description'];
        $product->category_id = $validatedData['category_id'];
        $product->price = $validatedData['price'];
        $product->stock = $validatedData['stock'];
        $product->image_path = $validatedData['image_path'] ?? $product->image_path;
        $product->active = $validatedData['active'];
        $product->save();

        return response()->json([
            'status' => true,
            'message' => 'Producto actualizado correctamente',
            'value' => $product,
        ]);

    }


    public function destroy(string $id)
    {
        try {

            $product = Product::find($id);
            if(!$product){
                return ApiResponse::NotFound('Product not found', [], 404);
            }
            $product->active = false;
            $product->save();
            $data = [
                'product' => $product,
            ];
            return ApiResponse::success('isSuccess', $data);

        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }


}
