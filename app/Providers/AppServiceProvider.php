<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->response();
    }

    public function response()
    {
        Response::macro('success', function ($message, $data = []) {
            $res = [
                'message'       =>  $message,
                'status'        =>  true,
                'errors'        =>  [],
                'data'          =>  $data,
            ];
            return response()->json($res, 200);
        });

        Response::macro('badRequest', function ($message, $errors = []) {
            $res = [
                'message'       =>  $message,
                'status'        =>  false,
                'errors'        =>  $errors,
                'data'          =>  [],
            ];
            return response()->json($res, 400);
        });

        Response::macro('failedRequest', function ($th, $message = 'Server Error.') {
            Log::error($th->getMessage()); // save error log
            $res = [
                'error'       =>  $message,
                'status'        =>  false,
            ];
            return response()->json($res, 500);
        });
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        $theme = config('ui.theme', 'legacy');
        if (!in_array($theme, config('ui.themes', []), true)) {
            $theme = 'legacy';
        }

        View::share('uiTheme', $theme);
    }
}
