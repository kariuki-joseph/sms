<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExports implements FromCollection, WithHeadings
{
    public function headings():array{
        return [
            'Id',
            'Name',
            'Email',
            'Type',
            'Registered At'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::all('id','name', 'email', 'type', 'created_at');
    }
}
