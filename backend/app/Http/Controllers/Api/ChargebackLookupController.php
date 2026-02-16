<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ChargebackLookupController extends Controller
{
    public function lookups()
    {
        $carriers = DB::table('carrier')
            ->selectRaw('id_carrier as id, carrier_name as name')
            ->whereRaw('COALESCE(is_deleted,0) = 0')
            ->orderBy('carrier_name')
            ->get();

        $sources = DB::table('bill_chargebacksources')
            ->selectRaw('idbill_chargebacksources as id, sourcedescript as name')
            ->orderBy('idbill_chargebacksources')
            ->get();

        return response()->json([
            'carriers' => $carriers,
            'sources' => $sources,
        ]);
    }
}
