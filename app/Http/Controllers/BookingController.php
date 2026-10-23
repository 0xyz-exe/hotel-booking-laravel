<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request) { return view('bookings.index', ['bookings' => $request->user()->bookings()->with('room.hotel')->latest()->paginate(10)]); }

    public function store(\App\Http\Requests\StoreBookingRequest $request, \App\Models\Room $room)
    {
        $data = $request->validated();
        $booking = \Illuminate\Support\Facades\DB::transaction(function () use ($room, $data, $request) {
            $room = \App\Models\Room::whereKey($room->id)->lockForUpdate()->firstOrFail();
            abort_unless($room->active, 404);
            if ($data['guests'] > $room->capacity) throw \Illuminate\Validation\ValidationException::withMessages(['guests' => 'Guest count exceeds room capacity.']);
            $nights = \Illuminate\Support\Carbon::parse($data['check_in'])->diffInDays(\Illuminate\Support\Carbon::parse($data['check_out']));
            $conflicts = \App\Models\Booking::where('room_id', $room->id)->whereIn('status', ['confirmed','pending'])->whereDate('check_in','<',$data['check_out'])->whereDate('check_out','>',$data['check_in'])->count();
            if ($conflicts >= $room->inventory) throw \Illuminate\Validation\ValidationException::withMessages(['room' => 'No rooms available for those dates.']);
            return \App\Models\Booking::create([...$data, 'room_id' => $room->id, 'user_id' => $request->user()->id, 'reference' => strtoupper(\Illuminate\Support\Str::random(12)), 'total_minor' => $room->nightly_rate_minor * $nights, 'status' => 'confirmed']);
        });
        return redirect()->route('bookings.index')->with('success', 'Booking '.$booking->reference.' confirmed.');
    }

    public function cancel(Request $request, \App\Models\Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
        abort_unless($booking->status === 'confirmed' && $booking->check_in->isFuture(), 422);
        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Reservation cancelled.');
    }
}
