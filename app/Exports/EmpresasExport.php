<?php

namespace App\Exports;

use App\Empresa;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class EmpresasExport implements FromView
{
    public function view(): View
    {
        $data = DB::table('pendiente')
        ->select('*')
        ->where('activo', true)
        ->get();

        return view('excel', [
            'Pendientes' => $data
        ]);
    }
}