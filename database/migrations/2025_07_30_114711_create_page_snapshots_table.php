<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('html_path')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['page_id', 'created_at']);
            $table->index(['page_id', 'version']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('current_snapshot_id')->nullable()->constrained('page_snapshots')->nullOnDelete();
            $table->integer('current_snapshot_version')->nullable();
        });
    }
};
