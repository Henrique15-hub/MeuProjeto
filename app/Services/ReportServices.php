<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;

class ReportServices
{
    private $entry;

    private $withdraw;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function total()
    {
        $wallet = $this->user->wallet()->first();

        $this->entry = $wallet->transactions()->where('type', 'entry')->get()->sum('amount');

        $this->withdraw = $wallet->transactions()->where('type', 'withdraw')->get()->sum('amount');

        $balance = $this->entry - $this->withdraw;

        $todayReport = Report::where('user_id', $this->user->id)
            ->where('date', 'all')
            ->where('value', $balance)
            ->where('type', 'total')->first();

        if ($todayReport) {
            return $balance;
        }

        Report::create([
            'user_id' => $this->user->id,
            'date' => 'all',
            'value' => $balance,
            'type' => 'total',
        ]);

        return $balance;
    }

    public function balanceMonth($year, $month)
    {
        $wallet = $this->user->wallet()->first();

        $initialData = Carbon::createFromDate($year, $month, 1)->toDateString();
        $finalData = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $this->entry = $wallet->transactions()
            ->where('date', '>=', $initialData)
            ->where('date', '<=', $finalData)
            ->where('type', 'entry')
            ->get()
            ->sum('amount');

        $this->withdraw = $wallet->transactions()
            ->where('date', '>=', $initialData)
            ->where('date', '<=', $finalData)
            ->where('type', 'withdraw')
            ->get()
            ->sum('amount');

        $balance = $this->entry - $this->withdraw;

        $date = Carbon::createFromDate($year, $month, 1)->format('Y-m');

        $monthReport = Report::where('user_id', $this->user->id)
            ->where('date', $date)
            ->where('value', $balance)
            ->where('type', 'month')->first();

        if ($monthReport) {
            return $balance;
        }

        Report::create([
            'user_id' => $this->user->id,
            'date' => $date,
            'value' => $balance,
            'type' => 'month',
        ]);

        return $balance;
    }

    public function personalized($initialData, $finalData)
    {
        $wallet = $this->user->wallet()->first();

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

        $personalizedReport = Report::where('user_id', $this->user->id)
            ->where('date', "{$initialData}==>{$finalData}")
            ->where('value', $balance)
            ->where('type', 'personalized')->first();

        if ($personalizedReport) {
            return $balance;
        }

        Report::create([
            'user_id' => $this->user->id,
            'date' => "{$initialData}==>{$finalData}",
            'value' => $balance,
            'type' => 'personalized',
        ]);

        return $balance;
    }
}
