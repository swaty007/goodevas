<?php

declare(strict_types=1);

namespace App\Facades\Ysell;

class YsellApiService extends YsellApiMethods
{
    public function getAllProducts()
    {
        $products = [];
        $page = 1;
        $perPage = 50;
        $endList = true;
        while ($endList) {
            $productPage = $this->getProductList($page, $perPage);
            dd($productPage);
            $endList = false;
        }
    }
}