<?php

namespace App\Exports;

use App\Roles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RolesExports implements FromCollection, WithHeadings
{
    public function headings():array{
        return [
            'Id',
            'Name',
            'Role'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Roles::all('id', 'name', 'role');
    }
}
