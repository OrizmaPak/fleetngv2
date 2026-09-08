<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
class ReportController extends Controller
{

    public function report_index(Request $request){
        if ($request->ajax()){ 
            $response = ApiModel::getTripReport($request->truck_id, $request->daterange, $request->trip_type);
            return $response;
        }      
        return view('/admin/report-management/report-detail');
    }

    
  public function report_pdf_download(Request $request){
    $response = ApiModel::getTripReport($request->truck_id, $request->daterange ,$request->trip_type);
    if($response['data'] == null || $response['data'] == []){
      return redirect()->back()->with('fail' , 'Record not found');
    }

    $data = ['response'=> $response];
    $pdf = PDF::Make();
    $pdf->loadView('/admin/report-management/report-pdf', $data);
    return $pdf->download('report_'.Carbon::now().'.pdf');

  }

}
