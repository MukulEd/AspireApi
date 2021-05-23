<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class LoanApplication extends Model
{
    // use HasFactory;
    use SoftDeletes;
    
    const STATUS_PENDING='pending';
    const STATUS_APPROVED='approved';
    CONST STATUS_CANCELLED='cancelled';
    const STATUS_COMPLETED='completed';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function payments()
    {
       return $this->hasMany(LoanPayment::class);
    }

}
