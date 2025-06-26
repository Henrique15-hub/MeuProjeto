<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\ReportServices;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    private $reportservices;

    public function __construct()
    {
        $this->reportservices = new ReportServices;
    }

    public function total(): JsonResponse
    {
        $balance = $this->reportservices->total();

        return response()->json([
            'message' => 'showing the balance of the account',
            'balance' => 'R$ '.number_format($balance, 2, ',', '.'),
        ]);
    }

    public function month(int $year, int $month): JsonResponse
    {
        if ($month > 12) {
            return ResponseHelper::withTip('invalid month', [], 400);
        }

        if ($year > date('Y')) {
            return ResponseHelper::withTip('invalid year', [], 400);
        }

        $balanceMonth = $this->reportservices->balanceMonth($year, $month);

        return response()->json([
            'message' => "showing the balance of the month {$month} of the year {$year}",
            'balance' => $balanceMonth,
        ]);
    }

    public function personalized($initialData, $finalData): JsonResponse
    {
        if (! Carbon::hasFormat($initialData, 'Y-m-d') or ! Carbon::hasFormat($finalData, 'Y-m-d')) {
            return ResponseHelper::withTip('invalid format or date', [], 400);
        }

        if (strtotime($initialData) > strtotime($finalData)) {
            return ResponseHelper::withTip('initial date has to be smaller than final date', [], 400);
        }

        $personalizedBalance = $this->reportservices->personalized($initialData, $finalData);

        return response()->json([
            'message' => "showing balance of {$initialData} to {$finalData}",
            'balance' => $personalizedBalance,
        ]);
    }
}
