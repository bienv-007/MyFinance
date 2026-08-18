<?php

namespace App\Console\Commands;

use App\Services\BudgetCycleService;
use Illuminate\Console\Command;

class ArchiveCompletedBudgets extends Command
{
    protected $signature = 'budgets:archive-completed';

    protected $description = 'Archive les budgets épuisés ou arrivés à échéance';

    public function handle(BudgetCycleService $cycles): int
    {
        $cycles->archiveCompletedAndExpiredBudgets();

        return self::SUCCESS;
    }
}
