<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkConsumeRecord extends Model
{
    use HasFactory;

    public function drink()
    {
        return $this->belongsTo(Drink::class);
    }

    public static function availableLimitForToday()
    {
        $limit = 500;
        $consumed = static::with('drink')->where('date', today())->get()->sum('drink.caffeine');

        return $limit - $consumed;
    }
}
