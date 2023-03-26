<?php

namespace App\Helpers;

use App\Jobs\SendAppointmentEmailJob;
use App\Models\Appointment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use TCPDF;

class AppointmentPdf 
{
    public static function generatePdf(Appointment $appointment, int $quantity, $subject = 'Appointment Complete')
    {
        $pdfName = $appointment->user->name . '_' . now()->format('YmdHis') . '.pdf';
        dispatch(new SendAppointmentEmailJob($pdfName, $appointment, $quantity,$subject));

        return $pdfName;
    }
}
