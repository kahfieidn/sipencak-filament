<?php

namespace App\Models;

use App\Models\Periode;
use App\Models\Kegiatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kegiatan_id',
        'kode',
        'nama_sub_kegiatan',
        'pagu',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}
