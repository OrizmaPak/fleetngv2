<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Driver;
use App\Models\FrontModel;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TrackingController extends Controller
{

   //function used to show live tracking list
   public function tracking_list()
   {
    $live_tracking_url="https://app.zypsa.com/tracking_api.php";
    $live_tracking_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "track"        
    ];
    $live_tracking_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_params));

     $breadcrumbs = [
       ['link' => "/analytics", 'name' => "Home"],  ['name' => "Live Tracking List"]
     ];
     return view('/admin/tracking-management/tracking-list', [
       'breadcrumbs' => $breadcrumbs,
       'tracking_details' => $this->scopedVehicles($live_tracking_data['data']['vehicles'])
     ]);
   }

   public function live_tracking()
   {
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"],  ['name' => "Live Tracking List"]
    ];
    return view('/admin/tracking-management/live-tracking', [
      'breadcrumbs' => $breadcrumbs
    ]);
   }

    // this function is used for ajax call to show trip list detail
  public function tracking_list_detail(Request $request)
  {
    $driversSerialNo = \App\Support\StaffAccess::drivers($request->user())->where('is_active',1)->pluck('device_serial_number')->all();

    $live_tracking_url="https://app.zypsa.com/tracking_api.php";
    $live_tracking_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "track"        
    ];   
    $live_tracking_distance_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "extra"        
    ];    

    if ($request->ajax()) {
      $live_tracking_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_params));
      $live_tracking_distance_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_distance_params));
      $live_tracking_details = $live_tracking_data['data']['vehicles'];
      $live_tracking_distance_details=$live_tracking_distance_data['data']['vehicle_data'];
      $api_data = $live_tracking_data['data']['vehicles'];
      $api_data = array_replace_recursive($live_tracking_details, $live_tracking_distance_details);
      $data=[];
      
      $data = array_filter($api_data,function($temp_data) use($driversSerialNo){
        return in_array($temp_data['serial'],$driversSerialNo) == true;
      });

      return Datatables::of($data)
        ->addIndexColumn()      
        ->addColumn('truck_id', function ($data) {
          return  $data['registration_no'] ;
        })       
        ->addColumn('device_serial_number', function ($data) {
          return  $data['serial'] ;
        })
        ->addColumn('voice_number', function ($data) {
          return  $data['voice_no'] ;
        })
        ->addColumn('billing_term', function ($data) {
          return  $data['billing_term'] ;
        })
        ->addColumn('driver_name', function ($data) {
          return  $data['driver_name'] ;
        })
        ->addColumn('last_updated', function ($data) {
          return $this->getISTDateInNigeriaTime($data['last_update']);
        })
        ->addColumn('installation_date', function ($data) {
          return date("d/m/Y H:i:s", strtotime($data['installation_date']));
        })
        ->addColumn('distance', function ($data) {
          return number_format($data['today_distance'], 2)." KM Today"."<br/>".number_format($data['week_distance'], 2)." KM This Week";
        })
        ->addColumn('status', function ($data) {
          if ($data['device_status'] == 0) {
            return '<span class="stopped">Stopped</span>';
          } else if ($data['device_status'] == 1) {
            return '<span class="idling">Idling</span>';
          } else if ($data['device_status'] == 2) {
            return '<span class="moving">Moving</span>';
          }
          else if ($data['device_status'] == 4) {
            return '<span class="unreachable">Unreachable</span>';
          }
           else {
            return '<span class="inactive">Inactive</span>';
          }
        })
        ->addColumn('location', function ($data) {
          if(!empty($data['arial_distance']))
          {
            return '<a href="' . route('map-tracking', $data['device_id']) . '">'.$data['arial_distance']." KM from ".$data['place'].'</a>';
          }
          else
          {
            return 'Not Available';
          }
        })       
        ->rawColumns(['truck_id', 'status', 'device_serial_number', 'voice_number', 'billing_term', 'last_updated', 'installation_date', 'distance', 'location'])
        ->make(true);
    }
  }

  // this function is used for ajax call to show trip list detail according to filter
  public function tracking_list_detail_filter(Request $request)
  {
    $driversSerialNo = \App\Support\StaffAccess::drivers($request->user())->where('is_active',1)->pluck('device_serial_number')->all();

    $vehicle_status = $request->vehicle_status;
    $driver = trim($request->search_driver);
 
    $live_tracking_url="https://app.zypsa.com/tracking_api.php";
    $live_tracking_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "track"        
    ];   
    $live_tracking_distance_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "extra"        
    ];
    $live_tracking_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_params));
    $live_tracking_distance_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_distance_params));
    $live_tracking_details = $live_tracking_data['data']['vehicles'];
    $live_tracking_distance_details=$live_tracking_distance_data['data']['vehicle_data'];

    $filtered_data = array_replace_recursive($live_tracking_details, $live_tracking_distance_details);
    $search_text = $driver;
    $search_status = $vehicle_status;
    $data=array_filter( $filtered_data, function($var) use ($driversSerialNo) {
      return ( in_array($var['serial'], $driversSerialNo) == true );
    });

    if(!empty($search_text)){
      $data=array_filter( $data, function($var) use ($search_text) {
          return ( stripos($var['registration_no'], $search_text) !== false );
      });
    }
    else{
      $data=$data;
    }

    if(isset($search_status) && $search_status != 6){ // if search_status  == 6 means showing all data

      $data=array_filter( $data, function($var) use ($search_status) {
          return ( stripos($var['device_status'], $search_status) !== false );
      });
    }
    else{
      $data=$data;
    }

    return Datatables::of($data
    )
      ->addIndexColumn()  
      ->addColumn('truck_id', function ($data) {
        return  $data['registration_no'] ;
      })
      ->addColumn('device_serial_number', function ($data) {
        return  $data['serial'] ;
      })
      ->addColumn('voice_number', function ($data) {
        return  $data['voice_no'] ;
      })
      ->addColumn('billing_term', function ($data) {
        return  $data['billing_term'] ;
      })
      ->addColumn('driver_name', function ($data) {
        return  $data['driver_name'] ;
      })
      ->addColumn('last_updated', function ($data) {
        return $this->getISTDateInNigeriaTime($data['last_update']);
      })
      ->addColumn('installation_date', function ($data) {
        return date("d/m/Y H:i:s", strtotime($data['installation_date']));
      })
      ->addColumn('distance', function ($data) {
        return number_format($data['today_distance'], 2)." KM Today"."<br/>".number_format($data['week_distance'], 2)." KM This Week";
      })
    
      ->addColumn('status', function ($data) {

        if ($data['device_status'] == 0) {
          return '<span class="stopped">Stopped</span>';
        } else if ($data['device_status'] == 1) {
          return '<span class="idling">Idling</span>';
        } else if ($data['device_status'] == 2) {
          return '<span class="moving">Moving</span>';
        }
        else if ($data['device_status'] == 4) {
          return '<span class="unreachable">Unreachable</span>';
        }
         else {
          return '<span class="inactive">Inactive</span>';
        }
      })
      ->addColumn('location', function ($data) {

        if(!empty($data['arial_distance']))
        {
          return '<a href="' . route('map-tracking', $data['device_id']) . '">'.$data['arial_distance']." KM from ".$data['place'].'</a>';
        }
        else
        {
          return 'Not Available';
        }
      })      

      ->rawColumns(['truck_id', 'status', 'device_serial_number', 'voice_number', 'billing_term', 'last_updated', 'installation_date', 'distance', 'status', 'location'])
      ->make(true);
  }

   public function map_tracking(Request $request,$id)
   {
    $live_tracking_url="https://app.zypsa.com/tracking_api.php";
    $live_tracking_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "track"        
    ];   
    $live_tracking_distance_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "extra"        
    ];
    $live_tracking_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_params));
    $live_tracking_distance_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_distance_params));
    $live_tracking_details = $live_tracking_data['data']['vehicles'];
    $live_tracking_distance_details=$live_tracking_distance_data['data']['vehicle_data'];

    $data = $this->scopedVehicles(array_replace_recursive($live_tracking_details, $live_tracking_distance_details));

    if(!empty($id)){

     $vehicle_key = array_search($id, array_column($data, 'device_id'));
     abort_if($vehicle_key === false, 404);
    }
    $date_from=date("Y-m-d H:i:s", mktime(0,0,0));
    $date_to=date("Y-m-d H:i:s", mktime(23,59,59));
    $search_vehicle_map_url="https://app.zypsa.com/report_map_history_result.php?&action=map_history_json&device_id=".$id."&date_from=".$date_from."&date_to=".$date_to."&user_name=".rawurlencode(config('integrations.tracking_username'))."&hash_key=".rawurlencode(config('integrations.tracking_key'))."";
    $search_vehicle_map_params          = [       
        'user_name'=> config('integrations.tracking_username'),
        'hash_key' => config('integrations.tracking_key')
      ];
    $search_vehicle_map_data = FrontModel::callPostCurl($search_vehicle_map_url, json_encode($search_vehicle_map_params));
    if(isset($search_vehicle_map_data['data']['trip_data']))
    {
      foreach($search_vehicle_map_data['data']['trip_data'] as $trip_value)
      {
        $trip_data['lat_long'][]= $trip_value['latitude'].",".$trip_value['longitude']."|";
        
      }
      $trip_data_string =implode(',', $trip_data['lat_long']);
      $trip_details_string = str_replace('|,', '|', $trip_data_string);
      $lat_long=rtrim($trip_details_string,"|");
    }
    else{
      $lat_long='';
    }
    if($request->isMethod('post')){   

      $output['message']="Data Found";
      $output['vehicle_details']=$data[$vehicle_key];
      $output['lat_long']=$lat_long;

      return $output;
    }
     $breadcrumbs = [
       ['link' => "/analytics", 'name' => "Home"],  ['name' => "Map Tracking"]
     ];
     return view('/admin/tracking-management/map-tracking', [
       'breadcrumbs' => $breadcrumbs,
       'vehicle_details' => $data[$vehicle_key],
       'lat_long' => $lat_long
     ]);
   }

   public function map_history(Request $request)
   {
    $live_tracking_url="https://app.zypsa.com/tracking_api.php";
    $live_tracking_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "track"        
    ];   
    $live_tracking_distance_params          = [       
      'user_name'=> config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "extra"        
    ];
    $live_tracking_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_params));
    $live_tracking_distance_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_distance_params));
    $live_tracking_details = $live_tracking_data['data']['vehicles'];
    $live_tracking_distance_details=$live_tracking_distance_data['data']['vehicle_data'];

    $filtered_data = array_replace_recursive($live_tracking_details, $live_tracking_distance_details);

    $live_tracking_details = $this->scopedVehicles($live_tracking_details);
    $filtered_data = $this->scopedVehicles($filtered_data);
    if($request->isMethod('post')){

      $search_text = $request->search;
      $date_range = $request->date_range;
      
      if(!empty($date_range))
      {
      $date=explode("to",$date_range);
      $date_from=$date[0];
      $date_to=$date[1];
      $from_date=date('Y-m-d', strtotime($date_from)).' 00:00:00';
      $to_date=date('Y-m-d', strtotime($date_to)).' 23:59:59';
      }
      else{
      $date_from=date("Y-m-d");
      $date_to=date("Y-m-d");
      $from_date=date("Y-m-d H:i:s", mktime(0,0,0));
      $to_date=date("Y-m-d H:i:s", mktime(23,59,59));
      }

      if(!empty($search_text)){

        $driver_key = array_search($search_text, array_column($live_tracking_details, 'registration_no'));
        abort_if($driver_key === false, 404);
        $device_id=$live_tracking_details[$driver_key]['device_id'];
        $search_vehicle_url="https://app.zypsa.com/report_vehicle_distance_result.php?&data_format=json&device_id=".$device_id."&date_from=".$date_from."&date_to=".$date_to."&time_picker_from=00:00:00&time_picker_to=23:59:59&user_name=".rawurlencode(config('integrations.tracking_username'))."&hash_key=".rawurlencode(config('integrations.tracking_key'))."&group_hour=24group_mode=1";
        $search_vehicle_params          = [       
            'user_name'=> config('integrations.tracking_username'),
            'hash_key' => config('integrations.tracking_key')
          ];
        $search_vehicle_data = FrontModel::callPostCurl($search_vehicle_url, json_encode($search_vehicle_params));

        // ---------------------------------------
        // Fetch map history start
        // ---------------------------------------
        $start_date = \Carbon\Carbon::parse($from_date);
        $end_date = \Carbon\Carbon::parse($to_date);
        $interval_days = 5;

        $dates = [];
        while ($start_date <= $end_date) {

          $next_date = $start_date->copy()->addDays($interval_days);

          if ($next_date > $end_date) {
            $next_date = $end_date;
          }

          $dates[] = [
            'from_date' => $start_date->copy()->toDateTimeString(),
            'to_date' => $next_date->toDateTimeString()
          ];

          $start_date->addDays($interval_days);
        }

        $responses = Http::pool(function ($pool) use ($dates, $device_id) {
            foreach ($dates as $date) {
                $pool->timeout(20)->withOptions(['connect_timeout'=>5])->post("https://app.zypsa.com/report_map_history_result.php?&action=map_history_json&device_id=" . $device_id . "&date_from=" . $date['from_date'] . "&date_to=" . $date['to_date'] . "&user_name=".rawurlencode(config('integrations.tracking_username'))."&hash_key=".rawurlencode(config('integrations.tracking_key'))."");
            }
        });
        
        $api_response = array_reduce($responses, function($initial, $response)  {
          $response = $response->json();
          if (isset($response['data']['trip_data'])) {
            return array_merge($initial, $response['data']['trip_data']);
          }
          return $initial;
        }, []);

        $search_vehicle_map_data = [
          'data' => [
            'trip_data' => $api_response,
          ]
        ];
        // ---------------------------------------
        // Fetch map history end
        // ---------------------------------------

        if(!empty($search_vehicle_map_data['data']['trip_data']))
        {
          foreach($search_vehicle_map_data['data']['trip_data'] as $trip_value)
          {
            $trip_data['lat_long'][]= $trip_value['latitude'].",".$trip_value['longitude']."|";
            
          }
          $trip_data_string =implode(',', $trip_data['lat_long']);
          $trip_details_string = str_replace('|,', '|', $trip_data_string);
          $lat_long=rtrim($trip_details_string,"|");
          $trip_details['lat_long']=$lat_long;    
          $output =  array_merge( $search_vehicle_data , $trip_details);
          $output['vehicle_details']=$live_tracking_details[$driver_key];
          $output['from_date']=date('d-M-Y',strtotime($date_from));
          $output['to_date']=date('d-M-Y',strtotime($date_to));
          $output['aerial_distance']=$filtered_data[$driver_key]['arial_distance'];
          $output['place']=$filtered_data[$driver_key]['place'];
          
        }
        else{
          $output =  $search_vehicle_data;
        }
        return $output;
      }
    }
      
     $breadcrumbs = [
       ['link' => "/analytics", 'name' => "Home"],  ['name' => "Map Tracking"]
     ];
     return view('/admin/tracking-management/map-history', [
       'breadcrumbs' => $breadcrumbs,
       'vehicle_details' => $live_tracking_details
     ]);
   }

   private function scopedVehicles(array $vehicles): array
   {
      $serials = \App\Support\StaffAccess::drivers(auth()->user())->where('is_active',1)->pluck('device_serial_number')->all();
      return array_values(array_filter($vehicles, function ($vehicle) use ($serials) {
          return in_array($vehicle['serial'] ?? null, $serials);
      }));
   }

   private function getISTDateInNigeriaTime($date) {
      $convertedDate = new \DateTime($date, new \DateTimeZone('Asia/kolkata') );
      $convertedDate->setTimeZone(new \DateTimeZone('Africa/Algiers'));
      return $convertedDate->format('d/m/Y H:i:s');
   }
}
