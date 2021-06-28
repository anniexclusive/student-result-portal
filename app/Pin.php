<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    //Mass assignable attributes
	protected $fillable = ['count', 'results_id', 'use_status'];
}
