<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePriceNullableInCornPrices extends Migration
{
    public function up()
    {
        Schema::table('corn_prices', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('corn_prices', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable(false)->change();
        });
    }
}
