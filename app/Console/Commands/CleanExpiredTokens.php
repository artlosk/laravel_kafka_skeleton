<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class CleanExpiredTokens extends Command
{
    protected $signature = 'tokens:clean-expired';

    protected $description = 'Remove expired API tokens';

    public function handle()
    {
        $expiredTokens = PersonalAccessToken::where('expires_at', '<', now())->get();

        if ($expiredTokens->isEmpty()) {
            $this->info('No expired tokens found.');
            return;
        }

        $count = $expiredTokens->count();

        foreach ($expiredTokens as $token) {
            $this->line("Removing expired token for user: {$token->tokenable->email}");
            $token->delete();
        }

        $this->info("Successfully removed {$count} expired token(s).");
    }
}
