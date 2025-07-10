<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection; // Import Collection
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{

public function index(Request $request)
{
    // Get the start and end dates for the current calendar view
    $startDate = $request->get('start'); // Start date for the calendar view
    $endDate = $request->get('end'); // End date for the calendar view

    // Fetch database events
    $events = Event::whereBetween('start', [$startDate, $endDate])
                   ->orWhereBetween('end', [$startDate, $endDate])
                   ->get()
                   ->map(function ($event) {
                       return [
                           'id' => $event->id,
                           'title' => $event->title,
                           'start' => $event->start,
                           'end' => $event->end,
                       ];
                   })->toArray();  // Convert to array here

    // Fetch holidays from API within the selected range
    $holidays = Http::get("https://date.nager.at/api/v3/PublicHolidays/".date('Y')."/US")->json();
//    \Log::info($holidays); // Log the holidays to check the response
    $holidayEvents = collect($holidays)->filter(function ($holiday) use ($startDate, $endDate) {
        return $holiday['date'] >= $startDate && $holiday['date'] <= $endDate;
    })->map(function ($holiday) {
        return [
            'id' => 'holiday-' . $holiday['date'],
            'title' => $holiday['localName'],
            'start' => $holiday['date'],
            'backgroundColor' => '#ffcccb',
            'borderColor' => '#ff0000',
            'textColor' => '#000000',
            'editable' => false,
        ];
    })->toArray(); // Convert to array here

/* COMMENTED OUT BECAUSE IT WAS TOO SLOW
    // Fetch moon phase data for each day in the range
    $moonEvents = [];
    $currentDate = \Carbon\Carbon::parse($startDate);
    while ($currentDate->lte($endDate)) {
        $moonPhase = Http::get("https://api.farmsense.net/v1/moonphases/?d=" . $currentDate->timestamp)->json();
        $moonEvents[] = [
            'id' => 'moon-' . $currentDate->format('Y-m-d'),
            'title' => 'Moon Phase: ' . $moonPhase[0]['Phase'],
            'start' => $currentDate->format('Y-m-d'),
            'backgroundColor' => '#d4a3ff',
            'borderColor' => '#8b5cf6',
            'textColor' => '#000000',
            'editable' => false,
        ];

        // Move to the next day
        $currentDate->addDay();
    }
*/

    // Merge all events (convert all to arrays before merging)
//    $allEvents = array_merge($events, $holidayEvents, $moonEvents);
    $allEvents = array_merge($events, $holidayEvents);

    // Return the merged events as JSON
    return response()->json($allEvents);
}

    public function store(Request $request)
    {
        $event = Event::create($request->all());
        return response()->json($event);
    }

   public function update(Request $request, $id)
   {
       $event = Event::findOrFail($id);
    
       $event->update([
           'start' => $request->start,
           'end' => $request->end,
       ]);

       return response()->json(['message' => 'Event updated successfully']);
   }

}

