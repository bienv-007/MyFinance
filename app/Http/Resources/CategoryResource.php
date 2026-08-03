<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_categorie' => $this->id_categorie,
            'nom_categorie' => $this->nom_categorie,
        ];
    }
}
