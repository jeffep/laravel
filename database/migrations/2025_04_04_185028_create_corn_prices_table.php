<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCornPricesTable extends Migration
{
    public function up()
    {
        Schema::create('corn_prices', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->unique();
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('corn_prices');
    }
}
