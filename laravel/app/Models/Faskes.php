<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faskes extends Model
{
    /** @use HasFactory<\Database\Factories\FaskesFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
        'email',
        'website',
        'gambar',
        'waktu_buka',
        'waktu_tutup',
        'type',
        'layanan',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'layanan' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    public function getWaktuBukaAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }

    public function getWaktuTutupAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }

    public function setWaktuBukaAttribute($value)
    {
        $this->attributes['waktu_buka'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setWaktuTutupAttribute($value)
    {
        $this->attributes['waktu_tutup'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    /**
     * Scope a query to only include active faskes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive faskes.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to include all faskes (active and inactive).
     * Use with special parameter @Faskes to show all data.
     */
    public function scopeAll($query)
    {
        return $query;
    }
}
