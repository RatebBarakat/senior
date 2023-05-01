<?php

namespace App\Http\Controllers\Center;

use App\Http\Controllers\Controller;
use App\Models\CenterReport;
use App\Models\Donation;
use Illuminate\Http\Request;
use TCPDF;

class ReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $request->validate([
            'needed' => 'required|array'
        ]);

        $array_needed = [
            'bloodStocks' => 'getBloods',
            'employees' => 'getEmployees',
            'bloodRequests' => 'getBloodRequests'
        ];
    
        if ($request->has('needed')) {
            $needed = $request->input('needed');
            $data = [];

            foreach ($needed as $key) {
                if (array_key_exists($key, $array_needed)) {
                    $method = $array_needed[$key];
                    $data[$key] = $this->$method();
                }
            }

            $html = view('admin.center.reports-pdf', compact('data'))->render();

            $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
            $pdf->SetCreator('Your Name');
            $pdf->SetAuthor('Your Name');
            $pdf->SetTitle('My PDF');
            $pdf->SetSubject('Example');
        
            $pdf->SetFont('dejavusans', '', 12);
        
            $pdf->AddPage();
    
            $pdf->writeHTML($html, true, false, true, false, '');

            if ($request->input('save') == true) {
                $admin  = auth()->guard('admin')->user();
                $name = auth()->guard('admin')->user()->center->name.rand(10,15);
                $filename = "{$name}.pdf";
                $pdfContent = $pdf->Output($filename, 'S');

                if (!is_dir(storage_path('app/private/centerReports/'))) {
                    mkdir(storage_path('app/private/centerReports'), 0777, true);                    
                }
                $pdf->Output(storage_path('app/private/centerReports/'.$filename), 'F');
                
                CenterReport::create([
                    'file_name' => $filename,
                    'center_id' => $admin->center->id,
                    'admin_id' => $admin->id
                ]);
                 
            }            

            return response()->make($pdf->Output('rateb.pdf', 'I'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.'rateb.pdf'.'"'
            ]);
            }
    }
    private function getBloods()
    {
        return Donation::with('center')->where([
            ['center_id', '=',  auth()->guard('admin')->user()->center->id]
        ])->get();
    }

    private function getEmployees()
    {
        auth()->guard('admin')->user()->load('center','center.employees');
        $employees = auth()->guard('admin')->user()->center->employees;
        return $employees;
    }

    private function getBloodRequests()
    {
        return auth()->guard('admin')->user()->bloodRequests;
    }
}
