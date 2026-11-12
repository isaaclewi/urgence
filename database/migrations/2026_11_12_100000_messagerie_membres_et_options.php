<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Refonte de la messagerie :
 *  - discussion_members : qui a accès à quel groupe (services autorisés par l'admin, modérateurs…)
 *  - options de groupe : les services peuvent-ils écrire, comment demander l'accès
 *  - messages : réponses, modification, suppression traçable
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Membres des groupes ───────────────────────────────
        if (! Schema::hasTable('discussion_members')) {
            Schema::create('discussion_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('discussion_space_id')->constrained('discussion_spaces')->cascadeOnDelete();
                $table->string('member_type', 20);              // service | agent (admin) | citoyen
                $table->unsignedBigInteger('member_id');
                $table->string('role', 20)->default('member');  // member | moderator
                $table->boolean('can_post')->default(true);
                $table->string('status', 20)->default('active'); // active | pending | muted
                $table->unsignedBigInteger('last_read_message_id')->nullable();
                $table->unsignedBigInteger('invited_by')->nullable();
                $table->timestamps();

                $table->unique(['discussion_space_id', 'member_type', 'member_id'], 'disc_members_unique');
                $table->index(['member_type', 'member_id']);
            });
        }

        // ── Options des groupes ───────────────────────────────
        Schema::table('discussion_spaces', function (Blueprint $table) {
            if (! Schema::hasColumn('discussion_spaces', 'services_can_post')) {
                $table->boolean('services_can_post')->default(true);
            }
            if (! Schema::hasColumn('discussion_spaces', 'join_policy')) {
                // invite = seul l'admin ajoute des services · request = un service peut demander l'accès
                $table->string('join_policy', 20)->default('invite');
            }
        });

        // ── Messages : réponses, édition, suppression ─────────
        Schema::table('discussion_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('discussion_messages', 'reply_to_id')) {
                $table->unsignedBigInteger('reply_to_id')->nullable();
            }
            if (! Schema::hasColumn('discussion_messages', 'edited_at')) {
                $table->timestamp('edited_at')->nullable();
            }
            if (! Schema::hasColumn('discussion_messages', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable();
            }
            if (! Schema::hasColumn('discussion_messages', 'deleted_by_type')) {
                $table->string('deleted_by_type', 20)->nullable();
            }
            if (! Schema::hasColumn('discussion_messages', 'deleted_by_id')) {
                $table->unsignedBigInteger('deleted_by_id')->nullable();
            }
        });

        // ── Reprise de l'existant : on ne coupe pas l'accès à ceux qui participaient déjà ──
        $now = now();

        // 1) le service responsable du groupe devient modérateur
        foreach (DB::table('discussion_spaces')->whereNotNull('service_id')->get(['id', 'service_id']) as $space) {
            DB::table('discussion_members')->insertOrIgnore([
                'discussion_space_id' => $space->id,
                'member_type'         => 'service',
                'member_id'           => $space->service_id,
                'role'                => 'moderator',
                'can_post'            => true,
                'status'              => 'active',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
        }

        // 2) les services qui avaient déjà écrit dans un groupe y restent membres
        $deja = DB::table('discussion_messages')
            ->where('sender_type', 'service')
            ->select('discussion_space_id', 'sender_id')
            ->distinct()
            ->get();

        foreach ($deja as $ligne) {
            DB::table('discussion_members')->insertOrIgnore([
                'discussion_space_id' => $ligne->discussion_space_id,
                'member_type'         => 'service',
                'member_id'           => $ligne->sender_id,
                'role'                => 'member',
                'can_post'            => true,
                'status'              => 'active',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_members');

        Schema::table('discussion_spaces', function (Blueprint $table) {
            $table->dropColumn(['services_can_post', 'join_policy']);
        });

        Schema::table('discussion_messages', function (Blueprint $table) {
            $table->dropColumn(['reply_to_id', 'edited_at', 'deleted_at', 'deleted_by_type', 'deleted_by_id']);
        });
    }
};
