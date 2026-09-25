<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('price', 12, 2);
            $table->enum('type', ['rent', 'sale', 'shortlet']);
            $table->unsignedTinyInteger('bedrooms');
            $table->string('location');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->foreignId('agent_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['type', 'price']);
            $table->index(['bedrooms', 'price']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
