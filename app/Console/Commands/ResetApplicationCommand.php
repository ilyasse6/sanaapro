<?php

namespace App\Console\Commands;

use App\Models\Entreprise;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetApplicationCommand extends Command
{
    protected $signature = 'app:reset
                            {--force : Exécuter sans confirmation interactive}';

    protected $description = 'Vide toutes les données métier et conserve uniquement les comptes Super Admin';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Cette action supprime toutes les données (entreprises, clients, factures, etc.) sauf les Super Admin. Continuer ?')) {
            $this->info('Opération annulée.');

            return self::SUCCESS;
        }

        $superAdminProfileId = Profile::query()->where('nom', 'Super Admin')->value('id');

        if (! $superAdminProfileId) {
            $this->error('Profil « Super Admin » introuvable. Exécutez d\'abord les seeders de base.');

            return self::FAILURE;
        }

        $superAdminUserIds = DB::table('users')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.id_user')
            ->where('user_profiles.id_profile', $superAdminProfileId)
            ->where('user_profiles.actif', 1)
            ->distinct()
            ->pluck('users.id');

        if ($superAdminUserIds->isEmpty()) {
            $this->error('Aucun compte Super Admin trouvé. Opération annulée.');

            return self::FAILURE;
        }

        $this->info('Super Admin conservés : '.$superAdminUserIds->implode(', '));

        DB::transaction(function () use ($superAdminProfileId, $superAdminUserIds) {
            $this->purgeUsers($superAdminProfileId, $superAdminUserIds);
            $this->purgeEntreprisePermissions();
            $this->purgeEntreprises();
            $this->purgeRuntimeTables($superAdminUserIds);
        });

        $this->purgeUploadedFiles();

        $this->newLine();
        $this->info('Application réinitialisée. Seuls les comptes Super Admin ont été conservés.');
        $this->line('Comptes restants : '.User::query()->whereIn('id', $superAdminUserIds)->pluck('email')->implode(', '));

        return self::SUCCESS;
    }

    private function purgeUsers(int $superAdminProfileId, $superAdminUserIds): void
    {
        $this->line('→ Suppression des utilisateurs non Super Admin…');

        User::query()
            ->whereIn('id', $superAdminUserIds)
            ->update(['id_entreprise' => null]);

        DB::table('user_profiles')
            ->whereIn('id_user', $superAdminUserIds)
            ->where('id_profile', '!=', $superAdminProfileId)
            ->delete();

        DB::table('user_profiles')
            ->whereNotIn('id_user', $superAdminUserIds)
            ->delete();

        DB::table('personal_access_tokens')
            ->whereNotIn('tokenable_id', $superAdminUserIds)
            ->where('tokenable_type', User::class)
            ->delete();

        User::query()
            ->whereNotIn('id', $superAdminUserIds)
            ->delete();
    }

    private function purgeEntreprisePermissions(): void
    {
        $this->line('→ Suppression des permissions liées aux entreprises…');

        DB::table('profile_permissions')
            ->whereNotNull('id_entreprise')
            ->delete();
    }

    private function purgeEntreprises(): void
    {
        $count = Entreprise::query()->count();

        if ($count === 0) {
            $this->line('→ Aucune entreprise à supprimer.');

            return;
        }

        $this->line("→ Suppression de {$count} entreprise(s) et données associées…");

        Entreprise::query()->delete();
    }

    private function purgeRuntimeTables($superAdminUserIds): void
    {
        $this->line('→ Nettoyage sessions, cache, files d\'attente et notifications…');

        $tables = [
            'sessions',
            'password_reset_tokens',
            'notifications',
            'jobs',
            'job_batches',
            'failed_jobs',
            'cache',
            'cache_locks',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }

        DB::table('personal_access_tokens')
            ->where('tokenable_type', User::class)
            ->whereIn('tokenable_id', $superAdminUserIds)
            ->delete();
    }

    private function purgeUploadedFiles(): void
    {
        $this->line('→ Suppression des fichiers uploadés (storage/app/public)…');

        $disk = Storage::disk('public');
        $directories = $disk->directories();

        foreach ($directories as $directory) {
            $disk->deleteDirectory($directory);
        }

        foreach ($disk->files() as $file) {
            if (basename($file) === '.gitignore') {
                continue;
            }
            $disk->delete($file);
        }
    }
}
