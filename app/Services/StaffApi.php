<?php

namespace App\Services;

use App\Http\Controllers\Api\ApiController;
use App\Support\StaffAccess;
use Illuminate\Http\Request;

class StaffApi
{
    private const ENDPOINTS = ['admin-login','superadmin-login','forgot-password','change-password','add-driver','change-driver-pin','get-driver-detail','update-driver-detail','change-driver-status','delete-driver','get-trip-detail','cancel-trip','delete-trip','change-trip-driver','add-pickup-location','get-pickup-location-detail','update-pickup-location-detail','delete-pickup-location','change-pickup-location-status','get-user-detail','update-user-detail','add-user','change-user-status','delete-user','get-merchant-detail','update-merchant-detail','add-merchant','change-merchant-status','delete-merchant'];

    public function call($endpoint, array $params)
    {
        if (!in_array($endpoint,self::ENDPOINTS,true)) throw new \InvalidArgumentException('Unknown internal API endpoint.');
        $method = str_replace('-','_',$endpoint);
        $request = Request::create('/api/'.$endpoint,'POST',$params);
        $request->setUserResolver(function () { return auth()->user(); });
        StaffAccess::api($method,$request);
        $result = app(ApiController::class)->{$method}($request);
        $result = is_string($result) ? json_decode($result,true,512,JSON_THROW_ON_ERROR) : $result;
        if (!is_array($result)) throw new \RuntimeException('Invalid internal API response.');
        return $result;
    }
}
