<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $rooms = Room::with('hotel')->where('active', true)->whereHas('hotel', fn ($q) => $q->where('active', true))
        ->when($request->filled('city'), fn ($q) => $q->whereHas('hotel', fn ($h) => $h->where('city', 'like', '%'.$request->input('city').'%')))
        ->when($request->filled('guests'), fn ($q) => $q->where('capacity', '>=', $request->integer('guests')))
        ->when($request->filled('check_in') && $request->filled('check_out'), fn ($q) => $q->whereDoesntHave('bookings', fn ($b) => $b->whereIn('status', ['pending','confirmed'])->whereDate('check_in','<',$request->input('check_out'))->whereDate('check_out','>',$request->input('check_in'))))
        ->paginate(9)->withQueryString();
    return view('welcome', compact('rooms'));
});

Route::middleware('auth')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/rooms/{room}/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

Route::get('/dashboard', function () { return redirect()->route('bookings.index'); })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

