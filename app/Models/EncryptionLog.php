<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\File;

class EncryptionLog extends Model
{
    protected $fillable = ['file_id', 'user_id', 'encrypted_at'];
    public $timestamps = false;

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id', 'id_file');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
