<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = ['name', 'slug', 'level', 'description'];
    public function assignments(): HasMany { return $this->hasMany(PostAssignment::class); }
}
