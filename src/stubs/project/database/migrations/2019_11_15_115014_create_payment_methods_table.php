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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedSmallInteger('site_id');
            $table->string('type');
            $table->float('tax')->default(0);
            $table->float('new_tax')->default(0);
            $table->float('vat')->nullable();
            $table->string('overview')->nullable();
            $table->boolean('default')->default(0);
            $table->boolean('test_mode')->default(0);
            $table->boolean('active')->default(0);
            $table->integer('ord')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};
