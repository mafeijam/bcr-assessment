<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use App\Models\DrinkConsumeRecord;

class ApiController extends Controller
{
    public function index()
    {
        return Drink::all();
    }

    public function store(Drink $drink)
    {
        $todayLimitRemain = DrinkConsumeRecord::availableLimitForToday();

        if ($todayLimitRemain - $drink->caffeine < 0) {
            return response()->json([
                'message' => "Limit hit for [{$drink->name} ({$drink->caffeine}mg)]<br>Remain: {$todayLimitRemain}mg",
            ], 422);
        }

        DrinkConsumeRecord::create([
            'drink_id' => $drink->id,
            'date' => today()
        ]);

        return response()->json([
            'message' => "You have comsumed [$drink->name]"
        ]);
    }

    public function destroy(DrinkConsumeRecord $record)
    {
        $record->delete();

        return response()->json([
            'message' => "You have deleted [{$record->drink->name}]"
        ]);
    }
}
