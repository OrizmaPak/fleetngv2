<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class FrontModel extends Model
{
    public static function callPostCurl($url, $params)
    {
        $prefix = rtrim(config('app.url'), '/') . '/api/';
        if (strpos($url,$prefix) === 0 && is_array($params)) return app(\App\Services\StaffApi::class)->call(substr($url,strlen($prefix)),$params);
        if (parse_url($url,PHP_URL_HOST) === 'app.zypsa.com') abort_unless(config('integrations.tracking_enabled') && config('integrations.tracking_username') && config('integrations.tracking_key'),503,'Tracking provider is not connected.');
        $client = Http::timeout(20)->withOptions(['connect_timeout'=>5]);
        $response = is_string($params) ? $client->withBody($params,'application/json')->post($url) : $client->asForm()->post($url,$params);
        $result = $response->throw()->json();
        if (!is_array($result)) throw new \RuntimeException('The provider returned an unreadable response.');
        return $result;
    }

    public static function callFilePostCurl($url,$params) { return self::callPostCurl($url,$params); }
}
