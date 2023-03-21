<?php
namespace App\Helpers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use TCPDF;

class AppointmentPdf 
{
    public static function generatePdf(Appointment $appointment,int $quantity,$subject = 'appointment complete')
    {
            $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
            $pdf->SetCreator('Your Name');
            $pdf->SetAuthor('Your Name');
            $pdf->SetTitle('My PDF');
            $pdf->SetSubject('Example');
        
            $pdf->SetFont('dejavusans', '', 12);
        
            $pdf->AddPage();
    
            $html = "<h2>hello {$appointment->user->name}</h2>";
            $html.= "<p>thnk you for donating {$quantity} of blood of type {$appointment->blood_type} at center {$appointment->center->name}";
        
            $pdf->writeHTML($html, true, false, true, false, '');
        
            $pdfName = $appointment->user->name . now()->format('YmdHis') . '.pdf';
            $pdf->Output(storage_path('app/public/pdf/'.$pdfName), 'F');
        
            $body = 'Please find the attached PDF file.';
            $attachment = storage_path('app/public/pdf');
        
            $pdfContent = $pdf->Output($pdfName, 'S');
            Mail::send([], [], function ($message) use ($pdfContent, $pdfName, $appointment,$subject) {
                $message->to($appointment->user->email)
                        ->subject($subject)
                        ->attachData($pdfContent, $pdfName, [
                            'mime' => 'application/pdf',
                        ]);
            });

            return $pdfName;
            
    }
}
