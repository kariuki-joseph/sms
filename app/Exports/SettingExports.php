<?php

namespace App\Exports;

use App\Settings;
use Maatwebsite\Excel\Concerns\FromCollection;

class SettingExports implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Settings::all();
    }
}
