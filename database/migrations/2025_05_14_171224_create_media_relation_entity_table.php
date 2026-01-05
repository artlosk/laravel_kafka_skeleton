<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_relation_entity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->morphs('entity');
            $table->integer('order_column')->nullable();
            $table->timestamps();

            $table->unique(['media_id', 'entity_id', 'entity_type'], 'media_entity_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_relation_entity');
    }
};
