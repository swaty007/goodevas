<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class StoragesExport extends StringValueBinder implements FromView
{

    /**
     * @inheritDoc
     */
    public function view(): View
    {
        $rows = DB::connection('bigquery')
            ->table('product_report')
            ->where('date', request()->query('date'))
            ->get();

        $data = [];
        $product = $rows->first();
        foreach ($rows as $value){
            $value['ean'] = (string)$value['ean'];
            $data[$value['type']][] = $value;
        }
        ksort($data);
        foreach ($data as $index => $datum) {
            uasort($datum, function ($a, $b){
                if ($a['sort_order'] == $b['sort_order']) {
                    return 0;
                }
                return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
            });
            $data[$index]  = $datum;
        }
        return view('exports.storages', [
            'storages' => $data,
            'productData' => $product
        ]);
    }
}
