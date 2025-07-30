<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveToAutomationRules20250728 extends Migration
{
    public function up()
    {
        Schema::table('automation_rules', function (Blueprint $table) {
            $table->boolean('active')->default(true);
        });
    }

    public function down()
    {
        Schema::table('automation_rules', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
}
