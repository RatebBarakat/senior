<?php

namespace App\Exports;

use App\Models\Donation;
use Maatwebsite\Excel\Concerns\FromCollection;

class DonationExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Donation::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Quantity',
            'Blood Type',
            'Date',
            'Expire At',
            'Taken',
            'Appointment ID',
            'User ID',
            'Social ID',
            'Center ID',
            'Created At',
            'Updated At',
        ];
    }
}
