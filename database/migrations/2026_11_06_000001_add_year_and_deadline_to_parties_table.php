<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->nullable()->after('invitation_token')->index();
            $table->timestamp('deadline')->nullable()->after('year');
        });

        // Existing parties belong to the year they were created in.
        DB::table('parties')->orderBy('id')->each(function ($party) {
            DB::table('parties')->where('id', $party->id)->update([
                'year' => Carbon::parse($party->created_at ?? 'now')->year,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropIndex(['year']);
            $table->dropColumn(['year', 'deadline']);
        });
    }
};
