<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    // Tambahkan ini
    protected $primaryKey = 'id_file';

    // Laravel mengasumsikan ada kolom created_at & updated_at,
    // karena kita tidak mendefinisikannya di migrasi file, kita nonaktifkan
    public $timestamps = false;
}
