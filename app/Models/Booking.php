<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = ['user_id', 'room_id', 'reference', 'guest_name', 'guest_email', 'guests', 'check_in', 'check_out', 'total_minor', 'status'];
    protected function casts(): array { return ['check_in' => 'date', 'check_out' => 'date']; }
    public function room(): BelongsTo { return $this->belongsTo(Room::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
