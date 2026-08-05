<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RevenuPrevisionService
{
    public function queryFor(User $user): Builder
    {
        return $user->revenuPrevisions()->getQuery();
    }

    public function applyFilters(Builder $query, Request $request): array
    {
        $search = trim($request->string('search')->toString());
        $source = trim($request->string('source')->toString());
        $date = $request->string('date')->toString();
        $month = $request->string('mois')->toString();
        $minimumAmount = $request->input('montant_min');
        $maximumAmount = $request->input('montant_max');

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('source_previsionnelle', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('montant_previsionnel', 'like', "%{$search}%");
            });
        }

        $query->when($source !== '', fn (Builder $builder) => $builder->where('source_previsionnelle', 'like', "%{$source}%"));
        $query->when($date !== '', fn (Builder $builder) => $builder->whereDate('date_previsionnelle', $date));

        $monthNumber = (int) substr($month, 5, 2);

        if (preg_match('/^\d{4}-\d{2}$/', $month) === 1 && $monthNumber >= 1 && $monthNumber <= 12) {
            $monthDate = Carbon::createFromFormat('Y-m', $month);
            $query->whereBetween('date_previsionnelle', [
                $monthDate->copy()->startOfMonth()->toDateString(),
                $monthDate->copy()->endOfMonth()->toDateString(),
            ]);
        }

        $query->when(is_numeric($minimumAmount), fn (Builder $builder) => $builder->where('montant_previsionnel', '>=', $minimumAmount));
        $query->when(is_numeric($maximumAmount), fn (Builder $builder) => $builder->where('montant_previsionnel', '<=', $maximumAmount));

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';

        return [
            'search' => $search,
            'source' => $source,
            'date' => $date,
            'mois' => $month,
            'montant_min' => $minimumAmount,
            'montant_max' => $maximumAmount,
            'sort' => in_array($sort, ['date_previsionnelle', 'montant_previsionnel', 'source_previsionnelle'], true) ? $sort : 'date_previsionnelle',
            'direction' => $direction,
        ];
    }

    public function statistics(User $user): array
    {
        $today = today();
        $baseQuery = $this->queryFor($user);
        $monthStart = $today->copy()->startOfMonth()->toDateString();
        $monthEnd = $today->copy()->endOfMonth()->toDateString();
        $yearStart = $today->copy()->startOfYear()->toDateString();
        $yearEnd = $today->copy()->endOfYear()->toDateString();

        $aggregate = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COALESCE(SUM(montant_previsionnel), 0) as montant_total')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN date_previsionnelle BETWEEN ? AND ? THEN montant_previsionnel ELSE 0 END), 0) as montant_mois',
                [$monthStart, $monthEnd],
            )
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN date_previsionnelle BETWEEN ? AND ? THEN montant_previsionnel ELSE 0 END), 0) as montant_annee',
                [$yearStart, $yearEnd],
            )
            ->first();

        $unrealized = fn (): Builder => (clone $baseQuery)->unrealized();
        $next = $unrealized()
            ->withRealizedStatus()
            ->whereDate('date_previsionnelle', '>=', $today)
            ->orderBy('date_previsionnelle')
            ->orderBy('id_revenu_prevision')
            ->first();
        $mostUsedSource = (clone $baseQuery)
            ->select('source_previsionnelle')
            ->selectRaw('COUNT(*) as occurrences')
            ->groupBy('source_previsionnelle')
            ->orderByDesc('occurrences')
            ->orderBy('source_previsionnelle')
            ->first();
        $highest = (clone $baseQuery)
            ->withRealizedStatus()
            ->orderByDesc('montant_previsionnel')
            ->orderBy('date_previsionnelle')
            ->first();

        return [
            'total' => (int) ($aggregate?->total ?? 0),
            'montant_total' => (float) ($aggregate?->montant_total ?? 0),
            'montant_mois' => (float) ($aggregate?->montant_mois ?? 0),
            'montant_annee' => (float) ($aggregate?->montant_annee ?? 0),
            'attendus' => $unrealized()->whereDate('date_previsionnelle', '>=', $today)->count(),
            'expirees' => $unrealized()->whereDate('date_previsionnelle', '<', $today)->count(),
            'prochaine' => $next,
            'source_principale' => $mostUsedSource?->source_previsionnelle,
            'plus_elevee' => $highest,
        ];
    }
}
