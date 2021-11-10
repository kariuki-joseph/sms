<?php

namespace App\Exports;

use App\Subjects;
use Maatwebsite\Excel\Concerns\FromCollection;

class SubjectsExports implements FromCollection
{
    public function headings():array{
        return [
            'ID',
            'Subject Name',
            'Subject Id'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Subjects::all('id', 'name', 's_id');
    }
}
