<?php

namespace App\Models;

use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Periode extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'batasan_pagu',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class);
    }

}
