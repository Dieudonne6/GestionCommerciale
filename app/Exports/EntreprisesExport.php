<?php

namespace App\Exports;

use App\Models\Entreprise;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntreprisesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Entreprise::select('idE', 'nom', 'IFU')->get();
    }

    public function headings(): array
    {
        return ["IDE", "Nom", "IFU"];
    }
}
