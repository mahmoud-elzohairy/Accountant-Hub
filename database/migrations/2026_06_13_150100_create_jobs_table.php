<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('company_name');
            $table->text('company_about')->nullable();   // client/company information
            $table->string('short_description', 255);     // shown on the job card
            $table->longText('description');              // full description on detail page
            $table->json('required_skills')->nullable();  // array of skill strings
            $table->decimal('budget_min', 10, 2);
            $table->decimal('budget_max', 10, 2);
            $table->unsignedSmallInteger('delivery_days'); // expected delivery time (days)
            $table->date('deadline');
            $table->json('attachments')->nullable();       // placeholder for attachment metadata
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
