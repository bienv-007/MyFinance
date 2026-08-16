<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Budget;

class NotificationService
{
    public function createOnce(int $userId, string $type, string $title, string $content): Notification
    {
        return Notification::query()->firstOrCreate([
            'id_utilisateur' => $userId,
            'type' => $type,
            'titre' => $title,
            'contenu' => $content,
        ]);
    }

    public function notifyBudgetUsageThresholds(Budget $budget): void
    {
        $montantTotal = (float) $budget->montant_total;

        if ($montantTotal <= 0) {
            return;
        }

        $usage = (($montantTotal - (float) $budget->solde) / $montantTotal) * 100;

        foreach ([80, 90, 100] as $threshold) {
            if ($usage < $threshold) {
                continue;
            }

            $this->createOnce(
                $budget->id_utilisateur,
                "budget_utilise_{$threshold}",
                "Budget utilisé à {$threshold} %",
                "Le budget « {$budget->periode} » (réf. {$budget->id_budget}) a atteint {$threshold} % d’utilisation.",
            );
        }
    }
}
