<?php

namespace Modules\Report\Reports;

use Modules\Report\Http\Models\Report;

class TransactionReport
{
    /**
     * Coupon Report
     * @param object $request
     * @return array $response
     */
    public static function getReports()
    {
        $res = (new Report)->getTransactionReport(request()->from, request()->to, request()->customerName, request()->subscriptionCode);
        $report = [];
        if ($res) {
            foreach ($res as $value) {
                $report[] = [
                    'name' => $value?->user?->name ?? __('Unknown'),
                    'package_name' => $value?->package?->packageName,
                    'code' => $value?->package?->code,
                    'total' => formatNumber($value->totalPrice)
                ];
            }

            return $report;
        }
    }
}
