<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->longText('description');

            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('ticket_categories')->restrictOnDelete();

            $table->string('assigned_team_level')->nullable()->index(); // L1, L2, L3, vendor
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('impact')->index(); // wide, narrow
            $table->string('urgency')->index(); // high, low
            $table->string('priority_code')->index(); // P1-P4
            $table->string('priority_label');

            $table->string('source')->default('web')->index(); // web, email, whatsapp, phone
            $table->string('status')->default('open')->index();

            $table->timestamp('first_response_due_at')->nullable()->index();
            $table->timestamp('resolution_due_at')->nullable()->index();
            $table->timestamp('first_responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamp('sla_paused_at')->nullable();
            $table->unsignedInteger('sla_total_paused_minutes')->default(0);
            $table->boolean('is_sla_response_breached')->default(false)->index();
            $table->boolean('is_sla_resolution_breached')->default(false)->index();
            $table->timestamp('sla_warning_sent_at')->nullable();

            $table->string('root_cause')->nullable();
            $table->longText('resolution_note')->nullable();
            $table->longText('prevention_note')->nullable();
            $table->timestamp('user_confirmed_at')->nullable();
            $table->timestamp('reopened_at')->nullable();
            $table->unsignedInteger('reopen_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'priority_code']);
            $table->index(['assigned_agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
