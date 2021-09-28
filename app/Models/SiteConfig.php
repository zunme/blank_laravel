<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    use HasFactory;

    protected $table = 'site_configs';
    protected $fillable = [
        'config_name','config_val','config_val2'
    ];    
}
