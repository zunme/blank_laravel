<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';
    protected $fillable = [
        'name','num_of_rooms','member_per_room','num_of_winners','admission_fee','cancellation_fee','winnings',
        'plan_allowance','marketing_allowance','interval_min','sort_np','next_game_at','is_use'
    ];    
}