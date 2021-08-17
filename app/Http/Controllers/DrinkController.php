<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use App\Models\DrinkConsumeRecord;

class DrinkController extends Controller
{
    public function index()
    {
        $drinks = Drink::all();
        $records = DrinkConsumeRecord::with('drink')
                    ->orderBy('date', 'desc')
                    ->get();

        $todayLimitRemain = $records->where('date', date('Y-m-d'))
                                ->groupBy('date')
                                ->map->sum('drink.caffeine')->first();

        return inertia('Index', compact('drinks', 'records', 'todayLimitRemain'));
    }

    public function store(Drink $drink)
    {
        $todayLimitRemain = DrinkConsumeRecord::availableLimitForToday();

        if ($todayLimitRemain - $drink->caffeine < 0) {
            return back()->with('flash', [
                'title' => 'Warning',
                'message' => "Limit hit for [{$drink->name} ({$drink->caffeine}mg)]<br>Remain: {$todayLimitRemain}mg",
                'color' => 'text-pink'
            ]);
        }

        DrinkConsumeRecord::create([
            'drink_id' => $drink->id,
            'date' => today()
        ]);

        return back()->with('flash', [
            'title' => 'Message',
            'message' => "You have comsumed [$drink->name]",
            'color' => 'text-blue-8'
        ]);
    }

    public function destroy(DrinkConsumeRecord $record)
    {
        $record->delete();

        return back()->with('flash', [
            'title' => 'Message',
            'message' => "You have deleted [{$record->drink->name}]",
            'color' => 'text-blue-8'
        ]);
    }
}
