<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Route;
use URL;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            $string=['admin','superadmin'];
            $getCurrentRoute=URL::current();
            $url='';
            if (strpos($getCurrentRoute, 'admin') == true){
                $url='auth-user-login'; 
            }
            if (strpos($getCurrentRoute, 'superadmin') == true){
                $url='auth-superadmin-login';
            }
            if(empty($url)) $url = 'auth-user-login';
            return route($url); 
        }
    }
}
