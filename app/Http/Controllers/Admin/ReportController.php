<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class ReportController extends Controller
{
  public function report_index(Request $request)
  {
    if ($request->ajax()) {
      $response = ApiModel::getTripReport($request->truck_id, $request->daterange, $request->trip_type, $request->customer, $request->payment_type);
      return $response;
    }
    return view('/admin/report-management/report-detail');
  }

  public function report_pdf_download(Request $request)
  {
    $response = ApiModel::getTripReport($request->truck_id, $request->daterange, $request->trip_type, $request->customer, $request->payment_type);
    if ($response['data'] == null || $response['data'] == []) {
      return redirect()->back()->with('fail', 'Record not found');
    }

    if(count($response['data']['trips_detail']) > 550){
      return redirect()->back()->with('fail', 'PDF size too large.');
    }
    
    $data = ['response' => $response];

    $pdf = FacadePdf::loadView('/admin/report-management/report-pdf', $data);
    return $pdf->download('report_' . Carbon::now() . '.pdf');
  }
}