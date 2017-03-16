<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = ['photo_url'];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
