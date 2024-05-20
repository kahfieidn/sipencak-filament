<?php

namespace App\Models;

use App\Models\User;
use App\Models\Periode;
use App\Models\Program;
use App\Models\SubKegiatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_id',
        'program_id',
        'user_id',
        'kode',
        'nama_kegiatan',
        'pagu',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function sub_kegiatan()
    {
        return $this->hasMany(SubKegiatan::class);
    }
    
}
