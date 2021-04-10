<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation_User extends Model
{
    use HasFactory;

    public $table = 'reservation_user';
    protected $fillable = [
        'reservation_id',
        'user_id'
    ];

}

// App\Models\Reservation_User::create([
//     'reservation_id' => '4',
//     'user_id' => '3'
// ]);
// App\Models\Reservation_User::create([
//     'reservation_id' => '1',
//     'user_id' => '1'
// ]);

// User::create([
//     'membership_id' => 5915,
//     'access' => 'Admin',
//     'name' => 'Noah Smith',
//     'phone' => '7278716624',
//     'email' => 'nosmith16@gmail.com',
//     'password' => Hash::make('password123')
// ]);