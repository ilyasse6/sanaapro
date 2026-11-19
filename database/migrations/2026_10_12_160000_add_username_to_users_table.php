<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('prenom');
        });

        $pris = [];
        foreach (DB::table('users')->orderBy('id')->get(['id', 'email']) as $user) {
            $base = strtolower((string) strtok((string) $user->email, '@'));
            $base = preg_replace('/[^a-z0-9._-]/', '', $base) ?: 'user'.$user->id;
            $username = $base;
            for ($i = 2; in_array($username, $pris, true); $i++) {
                $username = $base.$i;
            }
            $pris[] = $username;
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
