<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffImageController extends Controller
{
    public function show(Request $request)
    {
        \App\Support\StaffAccess::staff($request->user());
        $path = (string)$request->input('path');
        abort_unless(preg_match('~^(driver|user|merchant)/[a-zA-Z0-9_-]+\.(png|jpg|jpeg|webp)$~',$path),404);
        $disk = Storage::disk(config('integrations.image_disk'));
        abort_unless($disk->exists($path),404);
        return $disk->response($path,null,['Cache-Control'=>'private, no-cache','X-Content-Type-Options'=>'nosniff']);
    }
}
