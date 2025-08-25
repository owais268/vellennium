<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Booking;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function availability($partnerId, Request $request){
        {
            $date = $request->date ?? now()->toDateString();
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;

            // partner's working hours for that weekday
            $availability = Availability::where('partner_id', $partnerId)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            if (!$availability) {
                return response()->json(['slots' => []]);
            }

            $period = CarbonPeriod::create(
                Carbon::parse($date.' '.$availability->start_time),
                "{$availability->slot_length} minutes",
                Carbon::parse($date.' '.$availability->end_time)
            );

            $slots = [];
            foreach ($period as $start) {
                $end = (clone $start)->addMinutes($availability->slot_length);
                $slots[] = [
                    'start' => $start->toDateTimeString(),
                    'end'   => $end->toDateTimeString(),
                    'available' => true,
                ];
            }

            $bookings = Booking::where('partner_id',$partnerId)
                ->whereDate('start_time',$date)
                ->whereIn('status',['pending','confirmed','in_progress'])
                ->get();

            foreach ($slots as &$slot) {
                foreach ($bookings as $booking) {
                    if ($slot['start'] < $booking->end_time && $slot['end'] > $booking->start_time) {
                        $slot['available'] = false;
                    }
                }
            }

            return response()->json([
                'partner_id' => $partnerId,
                'date' => $date,
                'slots' => $slots,
            ]);
        }

    }
    public function store(Request $request){
        $booking = Booking::create([
            'partner_id' => $request->partner_id,
            'customer_id' => $request->user()->id,
            'service_id' => $request->service_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'pending',
            'verification_code' => rand(100000,999999),
            'price' => $request->price,
        ]);
        return response()->json($booking, 201);
    }
    public function show($id)
    {
        return Booking::with(['partner','customer'])->findOrFail($id);
    }
    public function confirm($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status'=>'confirmed']);
        return response()->json($booking);
    }
    public function checkIn(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        if ($booking->verification_code !== $request->code) {
            return response()->json(['error'=>'Invalid code'],422);
        }
        $booking->update(['status'=>'in_progress']);
        return response()->json($booking);
    }
    public function complete($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status'=>'completed']);
        // TODO: trigger commission distribution
        return response()->json($booking);
    }
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status'=>'canceled']);
        // TODO: handle cancellation fee/refunds
        return response()->json($booking);
    }
}
