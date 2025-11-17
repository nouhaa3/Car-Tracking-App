<?php

namespace App\Exports;

use App\Models\Voiture;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VoituresExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Voiture::with('user')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Marque',
            'Modèle',
            'Année',
            'Kilométrage',
            'État',
            'Statut',
            'Propriétaire',
            'Date création',
        ];
    }

    /**
     * @param Voiture $voiture
     */
    public function map($voiture): array
    {
        return [
            $voiture->idVoiture,
            $voiture->marque,
            $voiture->modele,
            $voiture->annee,
            $voiture->kilometrage,
            $voiture->etat,
            $voiture->statut,
            $voiture->user ? $voiture->user->name : 'N/A',
            $voiture->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
