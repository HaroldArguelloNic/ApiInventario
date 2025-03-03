<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    //
    use HasFactory;
    protected $fillable = ['order_id',
        'numero_transacion_netbanking',
        'monto_transferido','fecha_pago',
        'customer_id',
        'usuario_verificador_id',
        'comentarios_revision'];

    public function user() {
        return $this->belongsTo(User::class, 'usuario_verificador_id') ;
    }
    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id') ;
    }

}
