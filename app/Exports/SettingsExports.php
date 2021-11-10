<?php

namespace App\Exports;

use App\Settings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SettingsExports implements FromCollection,WithHeadings
{
    public function headings():array{
        return [
            'School Name',
            'School Motto',
            'School Vission',
            'Mission Statement',
            'Email Address',
            'Phone No.',
            'Post Office Address'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Settings::all('sch_name', 'sch_motto','sch_vission','sch_mission','email','phone','po_address');
    }
}
