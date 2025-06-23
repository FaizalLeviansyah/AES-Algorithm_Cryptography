<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_file';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // ▼▼▼ TAMBAHKAN BLOK INI ▼▼▼
    protected $fillable = [
        'file_name_source',
        'file_name_finish',
        'file_path',
        'file_size',
        'password', // ini adalah kolom keterangan
        'tgl_upload',
        'username',
        'status',
        'bit',
    ];
}
