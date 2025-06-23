<?php

namespace App\Services;

class TransactionQueryServices
{
    public function queryData ($initialData, $finalData) {
        $queryData = $this->getTransactionsUser()
        ->where('date', '>=', $initialData)
        ->where('date', '<=', $finalData);


        return $queryData;
    }

    public function queryType ($type) {
          $queryType = $this->getTransactionsUser()
          ->where('type', $type);

          return $queryType;
    }

    public function queryCategory ($category) {
        $queryCategory = $this->getTransactionsUser()
        ->where('category_name', $category);

        return $queryCategory;
    }




    public function getTransactionsUser () {
        $wallet = auth()->user()->wallet()->first();

        $transacitons = $wallet->transactions()->get();

        return $transacitons;
    }
}
