<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'default_name',
        'default_email',
        'default_phone',
        'default_address_line1',
        'default_address_line2',
        'default_city',
        'default_postcode'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        $address_parts = array_filter([
            $this->default_address_line1,
            $this->default_address_line2,
            $this->default_city,
            $this->default_postcode
        ]);
        
        return implode(', ', $address_parts);
    }
} 