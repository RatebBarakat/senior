<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class SendAppointmentEmailJob implements ShouldQueue
{     
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $pdfName;
    protected $appointment;
    protected $subject;
    protected $quantity;

    /**
     * Create a new job instance.
     *
     * @param string $pdfPath
     * @param Appointment $appointment
     * @param string $subject
     */
    public function __construct(string $pdfName, Appointment $appointment,float|int $quantity, string $subject)
    {
        $this->pdfName = $pdfName;
        $this->appointment = $appointment;
        $this->subject = $subject;
        $this->quantity = $quantity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $appointment = $this->appointment;
        $subject = $this->subject;

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('Your Name');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('My PDF');
        $pdf->SetSubject('Example');

        $pdf->SetFont('dejavusans', '', 12);

        $pdf->AddPage();

        $html = "<h2>hello <span ".'style=color="blue"'.">{$appointment->user->name}</span></h2>";
        $html .= "<p>thank you for donating <span ".'style=color="blue"'.">{$this->quantity}</span> units of blood of type {$appointment->blood_type} 
        at <span ".'style=color="blue"'.">{$appointment->center->name}</span> center</p>";

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdfPath = storage_path('app/public/pdf/' . $this->pdfName);
        $pdf->Output($pdfPath, 'F');

        $pdfContent = $pdf->Output("{$appointment->user->name}.pdf",'S');
        try {
            $name = $appointment->user->email.".pdf";
            $pdfContent = $pdf->Output($name, 'S');
            Mail::send([], [], function ($message) use ($pdfContent, $name) {
                $message->to($this->appointment->user->email)
                        ->subject($this->subject)
                        ->attachData($pdfContent, $name, [
                            'mime' => 'application/pdf',
                        ]);
            });
        } catch (\Exception $e) {
            // Log::error('Error sending appointment email: ' . $e->getMessage());
        }
    }
}
