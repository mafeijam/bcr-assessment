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

        $consumed = $records->where('date', date('Y-m-d'))
                    ->groupBy('date')
                    ->map->sum('drink.caffeine')->first();

        $consumedCount = $records->where('date', date('Y-m-d'))
                        ->groupBy('drink.id')
                        ->map->count();

        $dailySummary = $records->groupBy('date')->map(fn ($r, $date) => [
            'date' => $date,
            'count' => $r->count(),
            'caffeine' => $r->sum('drink.caffeine')
        ])->values();

        return inertia('Index', compact('drinks', 'records', 'consumed', 'consumedCount', 'dailySummary'));
    }

    public function store(Drink $drink)
    {
        $avaliable = DrinkConsumeRecord::availableLimitForToday();

        if ($avaliable < $drink->caffeine) {
            return back()->with('flash', [
                'title' => 'Warning',
                'message' => "Limit hit for [{$drink->name} ({$drink->caffeine}mg)]<br>Remain: {$avaliable}mg",
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
