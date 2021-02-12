<?php

namespace App\Exports;

use App\Empresa;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmpresasExport implements FromCollection
{
	
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Empresa::all();
    }
}