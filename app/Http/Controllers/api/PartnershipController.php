<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partnership;
use App\Models\Event;
use App\Models\Payment;
use Carbon\Carbon;

class PartnershipController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
    }
    
    public function createEvent(Request $request)
    {
        $event_type = $request->type;
        $affiliate_code = $request->affiliate_code;

        $partnership_id = Partnership::where('affiliate_code', $affiliate_code)->pluck('id')->first();
        
        if(!$partnership_id)
        {
            return response()->json([
                'message' => 'Partner not found!'
            ], 404);
        }
        // if(!$partnership) {
        //     $response->getBody()->write("Affiliate not found!");
        //     $response = $response->withStatus(404);
        // }

        // Create a new event object
        $newEvent = new Event();
        $newEvent->type = $event_type;
        $newEvent->partnership_id = $partnership_id;
        
        // Save the event object to the database
        $newEvent->save();
        
        // Return a response to the client
        return response()->json([
            'new_event' => $newEvent
        ]);
    }

    public function chartData(Request $request)
    {
        // $response = $response->withHeader('Content-type', 'application/json');
        // $request_body = $request->getParsedBody();

        $event_type = $request->type;
        $partnership_id = $request->partnership_id;
    
        $partnership = Partnership::where('id', $partnership_id)
            ->select('id', 'student_id')->first();
        
        if(!$partnership_id)
        {
            return response()->json([
                'message' => 'Partner not found!'
            ], 404);
        }

        $partnership_id = $partnership->id;
        $student_id = $partnership->student_id;
        $days_from_today = 30;

        // Set Default Time to Last 30 Days
        if(array_key_exists('days_from_today', $request->all())) {
            $days_from_today = $request->days_from_today;
        }

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($days_from_today);
        $events = [];

        if($event_type == 'payment') {
            $events = Payment::where([
                ['from_student_id', '=', $student_id],
                ['created_at', '>=', $startDate],
                ['created_at', '<=', $endDate],
            ])->get();
        } else {
            $events = Event::where([
                ['type', '=', $event_type],
                ['partnership_id', '=', $partnership_id],
                ['created_at', '>=', $startDate],
                ['created_at', '<=', $endDate],
            ])->get();
        }

        $eventsPerDay = [];
        foreach ($events as $event) {
            $eventDate = $event->created_at->toDateString();
            if (array_key_exists($eventDate, $eventsPerDay)) {
                $eventsPerDay[$eventDate]++;
            } else {
                $eventsPerDay[$eventDate] = 1;
            }
        }
        
        // Return a response to the client
        return response()->json($eventsPerDay);
    }
}
