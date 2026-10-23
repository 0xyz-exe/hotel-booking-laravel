<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_make_a_valid_booking_and_only_see_own_reservations(): void
    {
        $guest = User::factory()->create();
        $room = $this->room();
        $this->actingAs($guest)->post(route('bookings.store', $room), [
            'check_in' => now()->addDays(4)->toDateString(), 'check_out' => now()->addDays(7)->toDateString(),
            'guests' => 2, 'guest_name' => $guest->name, 'guest_email' => $guest->email,
        ])->assertRedirect(route('bookings.index'));
        $this->assertDatabaseHas('bookings', ['user_id' => $guest->id, 'total_minor' => 43500, 'status' => 'confirmed']);
        $this->actingAs(User::factory()->create())->get(route('bookings.index'))->assertDontSee('Harbor House');
    }

    public function test_booking_rejects_over_capacity_and_overlapping_inventory(): void
    {
        $guest = User::factory()->create();
        $room = $this->room(['inventory' => 1]);
        $dates = ['check_in' => now()->addDays(4)->toDateString(), 'check_out' => now()->addDays(7)->toDateString(), 'guests' => 2, 'guest_name' => $guest->name, 'guest_email' => $guest->email];
        $this->actingAs($guest)->post(route('bookings.store', $room), [...$dates, 'guests' => 3])->assertSessionHasErrors('guests');
        Booking::create([...$dates, 'room_id' => $room->id, 'user_id' => $guest->id, 'reference' => 'EXISTING001', 'total_minor' => 43500, 'status' => 'confirmed']);
        $this->actingAs($guest)->post(route('bookings.store', $room), $dates)->assertSessionHasErrors('room');
    }

    private function room(array $overrides = []): Room
    {
        $hotel = Hotel::create(['name' => 'Harbor House', 'slug' => uniqid('hotel-'), 'description' => 'Hotel', 'city' => 'Portland', 'address' => '1 Wharf']);
        return $hotel->rooms()->create(array_merge(['name' => 'Garden King', 'description' => 'Room', 'capacity' => 2, 'nightly_rate_minor' => 14500, 'inventory' => 3], $overrides));
    }
}
