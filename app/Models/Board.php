<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AcademicClassModel;

class Board extends Model
{
    use HasFactory;
    protected $fillable = ['name'];


     public function classes(): HasMany
    {
        // IMPORTANT: If your class model is not named 'InstituteClass', 
        // you will need to change it here. For example: return $this->hasMany(YourClassModel::class);
        return $this->hasMany(AcademicClassModel::class, 'board_id');
    }
}