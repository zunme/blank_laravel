<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryRoom extends Model
{
    use HasFactory;
    protected $table = 'lottery_rooms';
    protected $fillable = [
        'room_id','room_no','game_at','lottery_num'
    ];    
}