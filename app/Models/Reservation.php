<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'reservation_code','reservable_type','reservable_id',
        'customer_name','customer_phone','party_size',
        'reservation_date','start_time','end_time','duration_minutes',
        'status','source','note','created_by'
    ];

    protected $casts = [
        'reservation_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function requirements()
    {
        return $this->hasMany(ReservationMaterialRequirement::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(ReservationInventoryMovement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper akses resource
    public function table()
    {
        return $this->belongsTo(DiningTable::class, 'reservable_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'reservable_id');
    }
}
