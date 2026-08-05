<?php

namespace App\Actions;

use App\Models\Depense;
use App\Models\DepensePrevision;
use Illuminate\Support\Facades\DB;

class ConvertDepensePrevisionToDepense
{
    public function execute(DepensePrevision $prevision): Depense
    {
        return DB::transaction(function () use ($prevision): Depense {
            $depense = Depense::create([
                'id_utilisateur' => $prevision->id_utilisateur,
                'id_categorie' => $prevision->id_categorie,
                'montant' => $prevision->montant_previsionnel,
                'date_depense' => $prevision->date_previsionnelle->toDateString(),
                'description' => $prevision->description,
            ]);

            $prevision->delete();

            return $depense->load('categorie');
        });
    }
}
