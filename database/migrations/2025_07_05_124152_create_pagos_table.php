<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->string('numero_transaccion_netbanking')->nullable();
            $table->decimal('monto_transferido', 10, 2)->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('usuario_verificador_id')->nullable()->constrained('users');
            $table->string('estado_pago')->default('pendiente_revision');
            $table->text('comentarios_revision')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
