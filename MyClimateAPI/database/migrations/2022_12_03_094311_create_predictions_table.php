<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->foreignId('sensor_id')->constrained('sensors')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('temperature_id')->constrained('temperatures')->onUpdate('cascade')->onDelete('cascade');
            $table->float('y_hat');
            $table->float('y_hat_lower');
            $table->float('y_hat_upper');
            $table->dateTime('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('predictions');
    }
};
