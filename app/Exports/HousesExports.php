<?php

namespace App\Exports;

use App\Houses;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HousesExports implements FromCollection,WithHeadings
{
    public function headings():array{
        return [
            'Id',
            'House Name',
            'House Capacity'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Houses::all('id','name','capacity');
    }
}
