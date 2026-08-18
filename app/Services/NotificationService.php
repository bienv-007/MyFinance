<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\DepensePrevision;
use App\Models\Notification;
use App\Models\RevenuPrevision;

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

    public function notifyDueAndExpiredPrevisions(): void
    {
        $today = today();

        DepensePrevision::query()
            ->whereDate('date_previsionnelle', $today)
            ->each(fn (DepensePrevision $prevision) => $this->notifyDepensePrevisionDue($prevision));

        DepensePrevision::query()
            ->whereDate('date_previsionnelle', '<', $today)
            ->each(fn (DepensePrevision $prevision) => $this->notifyDepensePrevisionExpired($prevision));

        RevenuPrevision::query()
            ->unrealized()
            ->whereDate('date_previsionnelle', $today)
            ->each(fn (RevenuPrevision $prevision) => $this->notifyRevenuPrevisionDue($prevision));

        RevenuPrevision::query()
            ->unrealized()
            ->whereDate('date_previsionnelle', '<', $today)
            ->each(fn (RevenuPrevision $prevision) => $this->notifyRevenuPrevisionExpired($prevision));
    }

    private function notifyDepensePrevisionDue(DepensePrevision $prevision): void
    {
        $this->createOnce(
            $prevision->id_utilisateur,
            "depense_prevision_echeance_{$prevision->id_depense_prevision}",
            'Prévision de dépense prévue aujourd’hui',
            sprintf('Votre prévision « %s » est prévue aujourd’hui.', $prevision->description),
        );
    }

    private function notifyDepensePrevisionExpired(DepensePrevision $prevision): void
    {
        $this->createOnce(
            $prevision->id_utilisateur,
            "depense_prevision_expiree_{$prevision->id_depense_prevision}",
            'Prévision de dépense expirée',
            sprintf('Votre prévision « %s » a expiré le %s. Pensez à la valider ou à la mettre à jour.', $prevision->description, $prevision->date_previsionnelle->format('d/m/Y')),
        );
    }

    private function notifyRevenuPrevisionDue(RevenuPrevision $prevision): void
    {
        $this->createOnce(
            $prevision->id_utilisateur,
            "revenu_prevision_echeance_{$prevision->id_revenu_prevision}",
            'Prévision de revenu prévue aujourd’hui',
            sprintf('Votre revenu prévu « %s » est attendu aujourd’hui.', $prevision->source_previsionnelle),
        );
    }

    private function notifyRevenuPrevisionExpired(RevenuPrevision $prevision): void
    {
        $this->createOnce(
            $prevision->id_utilisateur,
            "revenu_prevision_expiree_{$prevision->id_revenu_prevision}",
            'Prévision de revenu expirée',
            sprintf('Votre revenu prévu « %s » a expiré le %s. Pensez à le marquer comme perçu ou à le mettre à jour.', $prevision->source_previsionnelle, $prevision->date_previsionnelle->format('d/m/Y')),
        );
    }
}
