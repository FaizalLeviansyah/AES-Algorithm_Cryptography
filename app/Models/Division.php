<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    // Tambahkan baris ini
    public $timestamps = false;

    // Kita juga bisa tambahkan ini agar aman saat create()
    protected $fillable = ['division_name'];
}
