<?php

$uiTheme = env('UI_THEME', 'legacy');
$uiThemes = ['legacy', 'fleetng-modern'];

if (!in_array($uiTheme, $uiThemes, true)) {
    $uiTheme = 'legacy';
}

$viewPaths = [resource_path('views')];
if ($uiTheme !== 'legacy') {
    array_unshift($viewPaths, resource_path('views/themes/' . $uiTheme));
}

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */
    
    'paths' => $viewPaths,


    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */
    
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),
    /*
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        '/home2/comeands/public_html/fleetng/fleetngcom/storage/framework/views'
    ),*/

];
