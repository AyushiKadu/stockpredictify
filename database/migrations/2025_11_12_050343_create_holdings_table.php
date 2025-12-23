<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holdings', function (Blueprint $table) {
            // simple numeric primary key
            $table->id();

            // portfolio_id references portfolios.id (UUID string)
            $table->uuid('portfolio_id');
            $table->foreign('portfolio_id')
                  ->references('id')
                  ->on('portfolios')
                  ->onDelete('cascade');

            $table->string('symbol');
            $table->integer('quantity'); // whole shares
            $table->decimal('buy_price', 10, 2);
            $table->decimal('current_price', 10, 2)->nullable();
            $table->string('sector')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holdings');
    }
};