<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionsToCornPrices extends Migration
{
    public function up()
    {
        Schema::table('corn_prices', function (Blueprint $table) {
            $table->decimal('bid', 8, 2)->nullable()->after('price');
            $table->decimal('ask', 8, 2)->nullable()->after('bid');
        });
    }

    public function down()
    {
        Schema::table('corn_prices', function (Blueprint $table) {
            $table->dropColumn(['bid', 'ask']);
        });
    }
}
