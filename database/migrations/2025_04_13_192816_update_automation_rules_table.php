<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAutomationRulesTable extends Migration
{
    public function up()
    {
        Schema::table('automation_rules', function (Blueprint $table) {
            // Drop foreign key and device_id
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
            // Add location column
            $table->string('location')->nullable();
        });
    }

    public function down()
    {
        Schema::table('automation_rules', function (Blueprint $table) {
            // Restore device_id and foreign key
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            // Drop location
            $table->dropColumn('location');
        });
    }
}
