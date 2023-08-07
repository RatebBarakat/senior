<?php

namespace App\Http\Controllers\Admin;

use App\Imports\DonationImport;
use App\Models\CenterReport;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelController extends Controller
{
    public function index(): View {
        $excels = CenterReport::excel()->get();
        return view('admin.excel.index',compact('excels'));
    }

    public function create(): View {
        $admin = auth('admin')->user();
        $donations = Donation::where('center_id',$admin->center->id)->get();
        return view('admin.excel.create',compact('donations'));
    }

    public function inportExcel(Request $request)
    {
        $request->validate([
            'excel' => 'required',
        ]);
    
        if ($request->hasFile('excel')) {
            $data = Excel::toArray(new DonationImport, $request->file('excel'))[0];
            $headers = $data[0];
            unset($data[0]);
            $numberAdded = 0;
    
            foreach ($data as $row) {
                $entry = [];
                foreach ($row as $index => $value) {
                    $header = $headers[$index];
                    $entry[$header] = $value;
                    if ($header === 'date' || $header === 'expire_at') {
                        $entry[$header] = Carbon::parse(Date::excelToDateTimeObject($value))->format('Y-m-d');
                    }
                }
                
                try {
                    Donation::create($entry);
                    $numberAdded++;
                } catch (\Throwable $th) {
                    continue;
                }
            }
            return back()->with('success',"{$numberAdded} Donations created successfully");
        }
        return back()->with('error','no file');
    }
    
    
    
}
