<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Virtualroom extends Model
{
    use HasFactory;
    protected $table = 'test_rooms';
    protected $fillable = [
        'room_no','pos_no'
    ];    
}