<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GenericExport implements FromCollection, WithHeadings, WithStyles
{
    protected $tableName;
    
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function collection()
    {
        // Récupérer les colonnes de la table
        $columns = DB::getSchemaBuilder()->getColumnListing($this->tableName);
        
        // Récupérer les données de la table
        return DB::table($this->tableName)->get()->map(function ($row) use ($columns) {
            $row = (array) $row; // Convertir en tableau associatif
        
            // Appliquer une logique générale pour toutes les colonnes
            foreach ($columns as $column) {
                // Si le champ contient des numéros de téléphone ou des identifiants longs
                if (is_numeric($row[$column]) && strlen($row[$column]) > 5) {
                    $row[$column] = (string) $row[$column]; // Convertir en chaîne sans apostrophe
                }
        
                // Vérifier si la valeur est une adresse e-mail et s'assurer qu'elle est bien une chaîne
                if (filter_var($row[$column], FILTER_VALIDATE_EMAIL)) {
                    $row[$column] = (string) $row[$column];
                }
            }
        
            return $row;
        });
    }

    public function headings(): array
    {
        // Récupérer dynamiquement les noms des colonnes
        return DB::getSchemaBuilder()->getColumnListing($this->tableName);
    }

    public function styles(Worksheet $sheet)
    {
        // Mettre en forme les en-têtes
        $columnCount = count($this->headings());
    
        // Style des en-têtes
        for ($i = 1; $i <= $columnCount; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true); // Ajustement automatique de la largeur
            $sheet->getStyleByColumnAndRow($i, 1)->getFont()->setBold(true); // En-têtes en gras
            $sheet->getStyleByColumnAndRow($i, 1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Centrer les en-têtes
        }
    
        // Appliquer les bordures aux cellules
        $rowCount = count($sheet->toArray()) + 1; // Compter le nombre de lignes (en-têtes + données)
    
        // Appliquer des bordures noires à toutes les cellules
        foreach ($sheet->getRowIterator(1, $rowCount) as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN); // Bordure fine
                $cell->getStyle()->getBorders()->getAllBorders()->getColor()->setRGB('000000'); // Couleur noire
            }
        }
    
        // Style de l'en-tête
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFA500']]
            ],
        ];
    }
    
}
