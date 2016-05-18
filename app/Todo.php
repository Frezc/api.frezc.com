<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['title', 'type', 'start_at', 'urgent_at', 'deadline', 'priority', 'location', 'contents'];
}
