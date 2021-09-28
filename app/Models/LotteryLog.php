<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryLog extends Model
{
    use HasFactory;
    protected $table = 'lottery_logs';
    protected $fillable = [
        'lottery_room_id','pos_no','user_id','winning_price','is_winner'
    ];    
}