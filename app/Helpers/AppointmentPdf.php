<?php
namespace App\Helpers;

use App\Jobs\SendPdfByEmail;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use TCPDF;

class AppointmentPdf 
{
    public static function generatePdf(Appointment $appointment,int $quantity,$subject = 'appointment complete')
    {
        $pdfName = $appointment->user->name . now()->format('YmdHis') . '.pdf';
        dispatch(new SendPdfByEmail($appointment,$pdfName,$quantity,"pdf test"));
        return $pdfName;
    }
}
