<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_tag', function (Blueprint $table) {
            # DON'T NEED THE PRIMARY KEY; ONLY FOREIGN KEYS
            // $table->id();

            $table->foreignId('post_id')
                ->constrained('posts')
                ->cascadeOnDelete();
            
            $table->foreignId('tag_id')
                ->constrained('tags')
                ->cascadeOnDelete();


            # Neither the timestamps
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tag');
    }
};
