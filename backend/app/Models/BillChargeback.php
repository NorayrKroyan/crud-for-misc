<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillChargeback extends Model
{
    protected $table = 'bill_chargebacks';
    protected $primaryKey = 'idbill_chargebacks';
    public $timestamps = false;

    protected $fillable = [
        'chargebackdate',
        'credit',
        'debit',
        'description',
        'carrier_id',
        'source_id',
    ];
}
