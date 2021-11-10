<?php

namespace App\Exports;

use App\Classes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassesExports implements FromCollection,WithHeadings
{
    public function headings():array{
        return [
            'Id',
            'Class Name',
            'Class Capacity'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Classes::all('id', 'name', 'capacity');
    }
}
