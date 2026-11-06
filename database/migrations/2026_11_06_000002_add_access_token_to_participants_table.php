<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Each participant gets a private link to fill in their questionnaire
     * and, once the draw has happened, see who they're buying for.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('access_token', 32)->nullable()->unique()->after('edit_token');
        });

        DB::table('participants')->whereNull('access_token')->orderBy('id')->each(function ($participant) {
            DB::table('participants')->where('id', $participant->id)->update([
                'access_token' => Str::random(32),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropUnique(['access_token']);
            $table->dropColumn('access_token');
        });
    }
};
