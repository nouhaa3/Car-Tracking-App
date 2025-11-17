<?php

namespace App\Imports;

use App\Models\Voiture;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class VoituresImport implements ToModel, WithHeadingRow, WithValidation
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Voiture([
            'marque' => $row['marque'],
            'modele' => $row['modele'],
            'annee' => $row['annee'],
            'kilometrage' => $row['kilometrage'],
            'etat' => $row['etat'] ?? 'Bon',
            'statut' => $row['statut'] ?? 'En boutique',
            'user_id' => $this->userId,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'annee' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'kilometrage' => 'required|integer|min:0',
            'etat' => 'nullable|in:Excellent,Bon,Moyen,Mauvais',
            'statut' => 'nullable|in:En boutique,En location,En maintenance',
        ];
    }
}
