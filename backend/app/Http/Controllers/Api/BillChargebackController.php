<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillChargeback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillChargebackController extends Controller
{
    public function index()
    {
        $rows = DB::table('bill_chargebacks as bc')
            ->leftJoin('carrier as c', 'c.id_carrier', '=', 'bc.carrier_id')
            ->leftJoin('bill_chargebacksources as s', 's.idbill_chargebacksources', '=', 'bc.source_id')
            ->select([
                'bc.idbill_chargebacks',
                'bc.chargebackdate',
                'bc.credit',
                'bc.debit',
                'bc.description',
                'bc.carrier_id',
                'bc.source_id',
                DB::raw('c.carrier_name as carrier_name'),
                DB::raw('s.sourcedescript as source_name'),
            ])
            ->orderByDesc('bc.chargebackdate')
            ->orderByDesc('bc.idbill_chargebacks')
            ->get();

        return response()->json($rows);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $row = BillChargeback::create($data);

        return response()->json($this->one($row->idbill_chargebacks));
    }

    public function update(Request $request, $id)
    {
        $row = BillChargeback::findOrFail((int)$id);

        $data = $this->validated($request);

        $row->update($data);

        return response()->json($this->one($row->idbill_chargebacks));
    }

    public function destroy($id)
    {
        $row = BillChargeback::findOrFail((int)$id);
        $row->delete(); // hard delete (table has no is_deleted)
        return response()->json(['ok' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'chargebackdate' => ['nullable', 'date'],
            'credit' => ['required', 'numeric', 'min:0'],
            'debit' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:200'],
            'carrier_id' => ['nullable', 'integer', 'exists:carrier,id_carrier'],
            'source_id' => ['nullable', 'integer', 'exists:bill_chargebacksources,idbill_chargebacksources'],
        ]);
    }

    private function one(int $id)
    {
        return DB::table('bill_chargebacks as bc')
            ->leftJoin('carrier as c', 'c.id_carrier', '=', 'bc.carrier_id')
            ->leftJoin('bill_chargebacksources as s', 's.idbill_chargebacksources', '=', 'bc.source_id')
            ->select([
                'bc.idbill_chargebacks',
                'bc.chargebackdate',
                'bc.credit',
                'bc.debit',
                'bc.description',
                'bc.carrier_id',
                'bc.source_id',
                DB::raw('c.carrier_name as carrier_name'),
                DB::raw('s.sourcedescript as source_name'),
            ])
            ->where('bc.idbill_chargebacks', $id)
            ->first();
    }
}
