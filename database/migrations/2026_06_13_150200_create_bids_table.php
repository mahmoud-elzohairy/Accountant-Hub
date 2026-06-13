<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);              // proposed price
            $table->unsignedSmallInteger('delivery_days'); // estimated delivery time (days)
            $table->text('cover_letter');                  // proposal message
            $table->text('experience_summary');            // relevant experience
            $table->timestamps();

            // A user can submit only one bid per job.
            $table->unique(['job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
