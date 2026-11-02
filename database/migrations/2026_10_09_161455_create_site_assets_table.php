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
        Schema::create('site_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_key')->unique(); // e.g. home_hero_banner, service_program_01
            $table->string('title'); // e.g. Banner Utama Hero Beranda
            $table->string('page'); // e.g. Beranda, Layanan, Tentang Kami, Fasilitas
            $table->string('placement'); // e.g. Header Hero Beranda (Samping Teks Slogan)
            $table->string('image_path')->nullable(); // stored in storage/app/public/site-assets/...
            $table->text('default_url')->nullable(); // fallback URL if image_path is null
            $table->string('recommended_size')->nullable(); // e.g. 800x1000px, 1200x800px
            $table->text('description')->nullable(); // Penjelasan detail letak dan fungsinya di web
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_assets');
    }
};
