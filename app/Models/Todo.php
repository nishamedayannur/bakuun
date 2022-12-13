<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;
    protected $fillable = ['CompanyName','CompanyType','CompanyCode','Start','End','InvTypeCode','RatePlanCode','InvCount','CountType','ParentId'];
}
