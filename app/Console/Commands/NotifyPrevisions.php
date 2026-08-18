<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class NotifyPrevisions extends Command
{
    protected $signature = 'previsions:notify';

    protected $description = 'Crée les notifications des prévisions arrivant à échéance ou expirées';

    public function handle(NotificationService $notifications): int
    {
        $notifications->notifyDueAndExpiredPrevisions();

        return self::SUCCESS;
    }
}
