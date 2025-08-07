<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Responses\ApiResponse;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PagoController extends Controller
{
public function index(){
    try{
        $pagos = pago::all();
        if (!$pagos) {
            return ApiResponse::NotFound('Pago not found', [],404);
        }else
            $data = $pagos->map(function ($pago) {
                return [
                    'id' => $pago->id,
                    'order_id' =>$pago->order_id,
                    'numero_transacion_netbanking' => $pago->numero_transacion_netbanking,
                    'monto_transferido' => $pago->monto_transferido,
                    'fecha_pago' => $pago->fecha_pago,
                    'customer_id' => $pago->customer_id,
                    'usuario_verificador_id' => $pago->usuario_verificador_id,
                    'comentarios_revision' => $pago->comentarios_revision,
                ];
            });
            return ApiResponse::success('isSuccess',$data, 200);
    }
    catch(\Exception $exception){
        return $exception->getMessage();
    }
}

public function procesarPago(Request $request) {
    DB::beginTransaction();
    try {
        $Validator= Validator::make($request->all(), [
            'order_id'=> ['required',"integer"],
            'numero_transacion_netbanking'=> ['required',"string"],
            'monto_transferido'=> ['required',"numeric","min:0.01"],
            'fecha_pago'=> ['required',"date"],
            'customer_id'=> ['required',"integer"],
            'usuario_verificador_id'=> ["integer"],
            'comentarios_revision'=> ['nullable',"string"],

        ] );
        //Si la validación falla se envia el error
        if($Validator->fails()){
            $data=[
                'message'=>'Error en la validacion de los datos',
                'errors' => $Validator->errors(),
                'status' => 400,
            ];
            return response()->json($data, 400);
        }
        $fechaPago = Carbon::parse($request['fecha_pago'])->format('Y-m-d H:i:s');
        $newPago = Pago::create([
            'order_id' => $request['order_id'],
            'numero_transacion_netbanking'=> $request['numero_transacion_netbanking'],
            'monto_transferido'=> $request['monto_transferido'],
            'fecha_pago'=> $fechaPago,
            'customer_id'=> $request['customer_id'],
            'usuario_verificador_id'=> $request['usuario_verificador_id'],
            'comentarios_revision'=> $request['comentarios_revision'],
        ]);
        if(!$newPago){
            $data=[
                'message'=>'Error en la creacion del producto',
                'status'=>500,
            ];
            return response()->json($data,500);
        }
        $data=[
            'message'=>'Pago procesado',
            'pago'=>$newPago,
        ];
        DB::commit();
        return response()->json($data,200);

    }catch (\Exception $exception){
        DB::rollBack();
        return $exception->getMessage();
    }
}
public function showPago(string $id) {
    try {
        $pagos = Pago::find($id);
        if (!$pagos) {
            return ApiResponse::NotFound('Pago no encontradd', [],404);
        }
        $data =  [
                'id' => $pagos->id,
                'order_id'=> $pagos->order_id,
                'numero_transacion_netbanking'=> $pagos->numero_transacion_netbanking,
                'monto_transferido'=> $pagos->monto_transferido,
                'fecha_pago'=> $pagos->fecha_pago,
                'customer_id'=> $pagos->customer_id,
                'usuario_verificador_id'=> $pagos->usuario_verificador_id,
                'comentarios_revision'=> $pagos->comentarios_revision,
        ];
        return ApiResponse::Success('isSuccess', $data, 200);
    }
    catch (\Exception $exception){
        return $exception->getMessage();
    }
}
public function updatePago(Request $request, string $id) {
    DB::beginTransaction();
    try {
        $pago= Pago::find($id);
        if (!$pago) {
            return ApiResponse::NotFound('Pago no encontrado', [],404);
        }
        $data = $request->all();
        $pago->update($request->only(['comentarios_revision']));
        if(!$pago){
            $data=[
                'message'=>'Error en la actualizacion del producto',
                'status'=>500,
            ];
            return response()->json($data,500);
        }

        DB::commit();

    }
    catch (\Exception $exception) {
        DB::rollBack();
        return $exception->getMessage();
    }
    return ApiResponse::Success('Actualización Exitosa', $data, 200);
}

}
