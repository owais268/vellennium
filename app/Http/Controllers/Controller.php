<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    function formatResponse($code=200,$status,$message,$data=[]){
        return[
            "status"=>$status,
            "code"=>$code,
            "message"=>$message,
            "data"=>$data,
        ];
    }
    function validationError($code=200,$status,$message,$error=[]){
        return[
            "status"=>$status,
            "code"=>$code,
            "message"=>$message,
            "error"=>$error,
        ];
    }
}


/**
 * 
 * namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Availability;
use App\Models\Blackout;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BookingController extends Controller
{
    public function availability($partnerId, Request $request)
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

        // generate slots
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

        // remove blackout times
        $blackouts = Blackout::where('partner_id',$partnerId)
            ->whereDate('date',$date)->get();

        foreach ($slots as &$slot) {
            foreach ($blackouts as $b) {
                $bStart = Carbon::parse($date.' '.$b->start_time);
                $bEnd   = Carbon::parse($date.' '.$b->end_time);

                if ($slot['start'] < $bEnd && $slot['end'] > $bStart) {
                    $slot['available'] = false;
                }
            }
        }

        // remove existing bookings
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

    public function store(StoreBookingRequest $request)
    {
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
    function calculateCommissions($booking) {
    $user = $booking->user; // the customer who booked
    $amount = $booking->total_amount;

    $tiers = [
        1 => 0.03, // Tier One
        2 => 0.02, // Tier Two
        3 => 0.04  // Tier Three
    ];

    $currentUser = $user;

    for ($tier = 1; $tier <= 3; $tier++) {
        $referral = Referral::where('referred_user_id', $currentUser->id)->first();
        if (!$referral) break;

        $commissionAmount = $amount * $tiers[$tier];

        Commission::create([
            'user_id' => $referral->user_id,
            'booking_id' => $booking->id,
            'tier' => $tier,
            'amount' => $commissionAmount
        ]);

        $currentUser = $referral->user; // move up the referral chain
    }
}


 */
