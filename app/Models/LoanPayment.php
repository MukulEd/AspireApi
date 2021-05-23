<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanPayment extends Model
{
    // use HasFactory;
    use SoftDeletes;

    const STATUS_PENDING='pending';
    const STATUS_CANCELLED='cancelled';
    const STATUS_COMPLETED='completed';



    public function application()
    {
        return $this->belongsTo(LoanApplication::class,'loan_application_id');
    }
    // public function scopeLast($query)
    // {
    //     return $query->latest()->first();
    // }
}
