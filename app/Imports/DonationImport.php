<?php

namespace App\Imports;

use App\Models\Donation;
use Maatwebsite\Excel\Concerns\ToModel;

class DonationImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Donation([
            'quantity' => $row[1],
            'blood_type' => $row[2],
            'date' => $row[3],
            'expire_at' => $row[4],
            'taken' => $row[5],
            'appointment_id' => $row[6],
            'user_id' => $row[8],
            'center_id' => $row[10],
        ]);
    }
}
