<?php

namespace App\Exports;

use App\Exams;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExamsExports implements FromCollection, WithHeadings
{
    public function headings():array{
        return [
            'Id',
            'Exam Name',
            'Exam Id'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Exams::all('id','name', 'exam_id');
    }
}
