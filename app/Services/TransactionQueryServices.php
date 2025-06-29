<?php

namespace App\Services;

class TransactionQueryServices
{
    public function queryDate($initialDate, $finalDate)
    {
        $queryDate = $this->getTransactionsUser()
            ->where('date', '>=', $initialDate)
            ->where('date', '<=', $finalDate);

        return $queryDate;
    }

    public function queryType($type)
    {
        $queryType = $this->getTransactionsUser()
            ->where('type', $type);

        return $queryType;
    }

    public function queryCategory($category)
    {
        $queryCategory = $this->getTransactionsUser()
            ->where('category_name', $category);

        return $queryCategory;
    }

    public function getTransactionsUser()
    {
        $wallet = auth()->user()->wallet()->first();

        $transacitons = $wallet->transactions()->get();

        return $transacitons;
    }
}
