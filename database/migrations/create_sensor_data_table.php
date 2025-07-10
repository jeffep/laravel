<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained('sensors')->onDelete('cascade');
            $table->timestamp('time');
            $table->string('title'); // e.g. "temperature", "humidity", etc.
            $table->float('value');
            $table->timestamps();

            $table->index(['sensor_id', 'title', 'time']); // For efficient queries
        });
    }

    public function down()
    {
        Schema::dropIfExists('sensor_data');
    }
};
