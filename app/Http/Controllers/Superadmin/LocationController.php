<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FrontModel;
use App\Models\PickupLocation;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
  private $api_url;
  public function __construct()
  {
    $this->url = config('app.url');
    $this->api_url = $this->url . 'api/';
  }


  //function used to show pickup location list
  public function location_list()
  {
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"],  ['name' => "Pickup Location List"]
    ];
    return view('/admin/location-management/location-list', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }


  // this function is used for ajax call to show pickup location list detail
  public function pickup_location_list_detail(Request $request)
  {
    if ($request->ajax()) {
      if(session('user_role') == 1)
      {
        $data = PickupLocation::with('users')->orderBy('id', 'DESC')->get();
      }
      else
      {
        $data = PickupLocation::with('users')->where('user_id',auth()->id())->orderBy('id', 'DESC')->get();
      }
      
      return Datatables::of($data)
        ->addIndexColumn()


        ->addColumn('location', function ($data) {

          return $data->location;
        })

        ->addColumn('company_user', function ($data) {
          if(session('user_role')==1)
          {
            return '<a href="' . route('view-user', $data->user_id) . '" class="mr-1">'.$data->users->first_name . ' ' . $data->users->last_name.'</a>';
          }
          else
          {
            return $data->users->first_name . ' ' . $data->users->last_name;
          }
          
        })


        ->addColumn('is_active', function ($data) {
          if ($data->is_active == 1) {
            $btn = '<div class="badge badge-success">Active</div>';
          } else {
            $btn = '<div class="badge badge-danger">Inactive</div>';
          }
          return $btn;
        })


        ->addColumn('action', function ($data) {
          $base_url = url('/');

          if ($data->is_active == 1) {
            $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',0)"><i class="fas fa-unlock"></i></a>';
          } else {
            $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',1)"><i class="fas fa-lock"></i></a>';
          }


          $btn = '<a href="' . route('view-pickup-location', $data->id) . '" class="mr-1"><i class="fas fa-eye"></i></a><a href="' . route('edit-pickup-location', $data->id) . '" class="mr-1"><i class="fas fa-pen"></i></a>' . $lock_icon . '
                    <a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
          return $btn;
        })

        ->rawColumns(['location', 'is_active', 'action','company_user'])
        ->make(true);
    }
  }



  //function used to add pickup location details
  public function add_pickup_location(Request $request)
  {

    if ($request->isMethod('post')) {

      //Formatted address
      $formattedAddr = str_replace(' ', '+', $request->location);
      //Send request and receive json data by address
      $geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddr . '&sensor=false&key=AIzaSyDU6bmt7uOJ1WPpcveuiTjdOdf04w1zi_U');
      $output = json_decode($geocodeFromAddr);


      $url = $this->api_url . "add-pickup-location";

      $params          = [
        'location' => $request->location,
        'latitude' => $output->results[0]->geometry->location->lat,
        'longitude' => $output->results[0]->geometry->location->lng,
        'user_id' => Auth::id()
      ];

      $add_location_data = FrontModel::callPostCurl($url, $params);

      if ($add_location_data['success']) {
        return redirect(route('location-list'))->with('success', $add_location_data['message']);
      } else {
        Session::flash('fail', $add_location_data['message']);
        return redirect(route('add-pickup-location'));
      }
    } else {
      $breadcrumbs = [
        ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/pickup-location-list", 'name' => "Pickup Location List"],  ['name' => "Add Pickup Location"]
      ];

      return view('/admin/location-management/add-pickup-location', [
        'breadcrumbs' => $breadcrumbs
      ]);
    }
  }


  //function used to update pickup location
  public function edit_pickup_location(Request $request, $id)
  {

    if ($request->isMethod('post')) {

      $location =  explode(",", $request->input('loc'));

      $pickup_location = PickupLocation::where('id', $id)->first();

      if ($pickup_location->latitude !== $location[0]) {
        $address = $request->input('location');
        //Formatted address
        $formattedAddr = str_replace(' ', '+', $address);
        //Send request and receive json data by address
        $geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddr . '&sensor=false&key=AIzaSyDU6bmt7uOJ1WPpcveuiTjdOdf04w1zi_U');
        $output = json_decode($geocodeFromAddr);
        //Get latitude and longitute from json data
        $data['latitude']  = $output->results[0]->geometry->location->lat;
        $data['longitude'] = $output->results[0]->geometry->location->lng;
        $lat = $data['latitude'];
        $lng = $data['longitude'];
      } else {
        $lat = $pickup_location->latitude;
        $lng = $pickup_location->longitude;
      }

      $url = $this->api_url . "update-pickup-location-detail";

      $params          = [
        'location_id' => $id,
        'location' => $request->location,
        'latitude' => $lat,
        'longitude' => $lng
      ];

      $location_update = FrontModel::callPostCurl($url, $params);

      if ($location_update['success']) {
        return redirect(route('location-list'))->with('success', $location_update['message']);
      } else {
        Session::flash('fail', $location_update['message']);
        return redirect(route('edit-pickup-location', $id));
      }
    } else {
      $url = $this->api_url . "get-pickup-location-detail";

      $params          = array('location_id' => $id);
      $location_details = FrontModel::callPostCurl($url, $params);
      $breadcrumbs = [
        ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/location-list", 'name' => "Pickup Location"],  ['name' => "Edit Category"]
      ];
      return view('/admin/location-management/edit-pickup-location', [
        'breadcrumbs' => $breadcrumbs,
        'location_details' => $location_details['data']
      ]);
    }
  }

  //function used to view pickup location
  public function view_pickup_location(Request $request, $id)
  {
    $url = $this->api_url . "get-pickup-location-detail";

    $params          = array('location_id' => $id);
    $location_details = FrontModel::callPostCurl($url, $params);
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/location-list", 'name' => "Pickup Location"],  ['name' => "View Category"]
    ];
    return view('/admin/location-management/view-pickup-location', [
      'breadcrumbs' => $breadcrumbs,
      'location_details' => $location_details['data']
    ]);
  }

  //function used to delete pickup location 
  public function pickup_location_delete(Request $request)
  {

    $url = $this->api_url . "delete-pickup-location";

    $params          = array('location_id' => $request->location_id);
    $location_delete = FrontModel::callPostCurl($url, $params);

    if ($location_delete['success']) {
      return redirect(route('location-list'))->with('success', $location_delete['message']);
    } else {
      return redirect(route('location-list'))->with('fail', $location_delete['message']);
    }
  }

  //function used to change pickup location status
  public function pickup_location_status(Request $request)
  {
    $url = $this->api_url . "change-pickup-location-status";

    $params          = array('location_id' => $request->id, 'is_active' => $request->status);
    $location_status = FrontModel::callPostCurl($url, $params);
  }
}
