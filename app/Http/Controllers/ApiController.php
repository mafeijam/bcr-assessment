<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use App\Models\DrinkConsumeRecord;

class ApiController extends Controller
{
    public function index()
    {
        return [
            'drinks' => Drink::all(),
            'availableLimit' => DrinkConsumeRecord::availableLimitForToday(),
            'records' => DrinkConsumeRecord::with('drink')
                ->orderBy('date', 'desc')
                ->get()
        ];
    }

    public function store(Drink $drink)
    {
        $avaliable = DrinkConsumeRecord::availableLimitForToday();

        if ($avaliable < $drink->caffeine) {
            return response()->json([
                'message' => "Limit hit for [{$drink->name} ({$drink->caffeine}mg)]<br>Remain: {$avaliable}mg",
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
