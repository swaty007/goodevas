<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class StoragesExportForImport extends StringValueBinder implements FromCollection, WithHeadings
{
    protected Collection $rows;

    public function __construct()
    {
        $this->rows = DB::connection('bigquery')
            ->table('product_report')
            ->where('date', request()->query('date'))
            ->get();
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->rows->isNotEmpty() ? array_keys($this->rows->first()) : [];
    }
}
