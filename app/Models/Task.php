<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Allows these fields to be saved to the database 
    protected $fillable = ['title', 'due_date', 'priority', 'status'];
}