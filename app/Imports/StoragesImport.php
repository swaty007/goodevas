<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StoragesImport implements SkipsOnError, ToCollection, WithHeadingRow //ToModel, , WithBatchInserts, WithChunkReading
{
    use Importable;
    use SkipsErrors;

    public function __construct() {}

    public function collection(Collection $collection)
    {
        //        foreach ($collection as $key => $row) {
        //            $ean = $row['ean'];
        //            $product = DB::connection('bigquery')
        //                ->table('product_report')
        //                ->where('date', '2024-10-10')
        //                ->where('EAN', (string)$ean)
        //                ->get();
        //        }
        $products = $collection->toArray();
        $EANs = array_column($products, 'ean');
        $dates = array_column($products, 'date');
        foreach ($dates as $date) {
            if ($date !== $this->request->query('date')) {
                throw new \Exception('Invalid date');
            }
        }

        if (! empty($EANs)) {
            DB::connection('bigquery')
                ->table($this->table)
                ->where('date', $this->request->query('date'))
                ->whereIn('EAN', $EANs)
                ->delete();
            DB::connection('bigquery')
                ->table($this->table)->insert($products);
        }
    }

    //    public function batchSize(): int
    //    {
    //        return 500;
    //    }

    //    public function chunkSize(): int
    //    {
    //        return 1000;
    //    }

    public function onError(\Throwable $e)
    {
        Log::error('UsersImport Error: '.$e->getMessage());
        $this->errors[] = $e;
    }
}
