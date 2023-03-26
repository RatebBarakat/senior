<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use TCPDF;

class SendPdfByEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Appointment $appointment;
    protected int|float $quantity;
    protected string $subject;
    protected string $pdfName;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment,$pdfName,int|float $quantity,string $subject = "pdf")
    {
        $this->appointment = $appointment;
        $this->quantity = $quantity;
        $this->subject = $subject;
        $this->pdfName = $pdfName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
        $pdf->SetCreator('Your Name');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('My PDF');
        $pdf->SetSubject('Example');
    
        $pdf->SetFont('dejavusans', '', 12);
    
        $pdf->AddPage();

        $html = "<h2>hello <span ".'style=color="blue"'.">{$this->appointment->user->name}</span></h2>";
        $html.= "<p>thnk you for donating <span ".'style=color="blue"'.">{$this->quantity}</span> of blood of type {$this->appointment->blood_type} 
        at <span ".'style=color="blue"'.">{$this->appointment->center->name}</span> center";
    
        $pdf->writeHTML($html, true, false, true, false, '');
    
        $pdf->Output(storage_path('app/public/pdf/'.$this->pdfName), 'F');
    
        $name ="{$this->appointment->user->name}.pdf";
        $pdfContent = $pdf->Output($name, 'S');
        Mail::send([], [], function ($message) use ($pdfContent, $name) {
            $message->to($this->appointment->user->email)
                    ->subject($this->subject)
                    ->attachData($pdfContent, $name, [
                        'mime' => 'application/pdf',
                    ]);
        });

    }
}
