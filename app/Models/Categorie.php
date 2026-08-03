<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $primaryKey = 'id_categorie';

    protected $fillable = [
        'nom_categorie',
    ];

    public $timestamps = false;

    public function getRouteKeyName(): string
    {
        return 'id_categorie';
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class, 'id_categorie', 'id_categorie');
    }
}
