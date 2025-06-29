<?php

namespace App\Services;

use App\Models\Report;
use Carbon\Carbon;

class ReportServices
{
    private $entry;

    private $withdraw;

    public function total()
    {
        $wallet = auth()->user()->wallet()->first();

        $this->entry = $wallet->transactions()->where('type', 'entry')->get();

        $this->withdraw = $wallet->transactions()->where('type', 'withdraw')->get();

        $totalEntry = $this->entry->sum('amount');
        $totalWithdraw = $this->withdraw->sum('amount');

        $balance = $totalEntry - $totalWithdraw;

        $todayReport = Report::where('user_id', auth()->id())
            ->where('date', 'all')
            ->where('value', $balance)
            ->where('type', 'total')->first();

        if ($todayReport) {
            return $balance;
        }

        Report::create([
            'user_id' => auth()->id(),
            'date' => 'all',
            'value' => $balance,
            'type' => 'total',
        ]);

        return $balance;
    }

    public function balanceMonth($year, $month)
    {
        $wallet = auth()->user()->wallet()->first();

        $initialData = Carbon::createFromDate($year, $month, 1)->toDateString();
        $finalData = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $this->entry = $wallet->transactions()
            ->where('date', '>=', $initialData)
            ->where('date', '<=', $finalData)
            ->where('type', 'entry')
            ->get();

        $this->withdraw = $wallet->transactions()
            ->where('date', '>=', $initialData)
            ->where('date', '<=', $finalData)
            ->where('type', 'withdraw')
            ->get();

        $totalEntry = $this->entry->sum('amount');
        $totalWithdraw = $this->withdraw->sum('amount');

        $balance = $totalEntry - $totalWithdraw;

        $date = Carbon::createFromDate($year, $month, 1)->format('Y-m');
        $monthReport = Report::where('user_id', auth()->id())
            ->where('date', $date)
            ->where('value', $balance)
            ->where('type', 'month')->first();

        if ($monthReport) {
            return $balance;
        }

        Report::create([
            'user_id' => auth()->id(),
            'date' => $date,
            'value' => $balance,
            'type' => 'month',
        ]);

        return $balance;
    }

    public function personalized($initialData, $finalData)
    {
        $wallet = auth()->user()->wallet()->first();

        $this->entry = $wallet->transactions()
            ->where('date', '>=', $initialData)
            ->where('date', '<=', $finalData)
            ->where('type', 'entry')
            ->get();

        $this->withdraw = $wallet->transactions()
            ->where('date', '>=', $initialData)
            ->where('date', '<=', $finalData)
            ->where('type', 'withdraw')
            ->get();

        $totalEntry = $this->entry->sum('amount');
        $totalWithdraw = $this->withdraw->sum('amount');

        $balance = $totalEntry - $totalWithdraw;

        $personalizedReport = Report::where('user_id', auth()->id())
            ->where('date', "{$initialData}==>{$finalData}")
            ->where('value', $balance)
            ->where('type', 'personalized')->first();

        if ($personalizedReport) {
            return $balance;
        }

        Report::create([
            'user_id' => auth()->id(),
            'date' => "{$initialData}==>{$finalData}",
            'value' => $balance,
            'type' => 'personalized',
        ]);

        return $balance;
    }
}
