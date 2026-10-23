<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'city', 'address', 'active'];

    protected function casts(): array { return ['active' => 'boolean']; }

    public function rooms(): HasMany { return $this->hasMany(Room::class); }
}
