<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['hotel_id', 'name', 'description', 'capacity', 'nightly_rate_minor', 'facilities', 'inventory', 'active'];
    protected function casts(): array { return ['facilities' => 'array', 'active' => 'boolean']; }
    public function hotel(): BelongsTo { return $this->belongsTo(Hotel::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
}
