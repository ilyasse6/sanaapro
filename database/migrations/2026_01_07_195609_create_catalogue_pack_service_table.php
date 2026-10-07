<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogue_pack_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pack_id');
            $table->unsignedBigInteger('catalogue_service_id');
            $table->timestamps();

            $table->foreign('pack_id')->references('id')->on('catalogue_packs')->onDelete('cascade');
            $table->foreign('catalogue_service_id')->references('id')->on('catalogue_services')->onDelete('cascade');
            $table->unique(['pack_id', 'catalogue_service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogue_pack_service');
    }
};
