<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PolicyPageController extends Controller
{
    public function refund_policy(Request $request){
        $content = Page::where('name', 'Refund Policy')->first();
        if($request->isMethod('post')){
            $data = [
              "name" => "Refund Policy",
              "Title" => "Refund Policy",
              "contents" => $request->body,
            ];

            if($content){
                $content->update($data);
            }else{
                Page::create($data);
            }

            return redirect()->back()->with('success', 'Page content updated successfully!');
        }
        return view('superadmin.pages.refund-policy', compact('content'));
    }

    public function privacy_policy(Request $request){
        $content = Page::where('name', 'Privacy Policy')->first();
        if($request->isMethod('post')){
            $data = [
              "name" => "Privacy Policy",
              "Title" => "Privacy Policy",
              "contents" => $request->body,
            ];

            if($content){
                $content->update($data);
            }else{
                Page::create($data);
            }

            return redirect()->back()->with('success', 'Page content updated successfully!');
        }
        return view('superadmin.pages.privacy-policy', compact('content'));
    }

    public function terms_conditions(Request $request){
        $content = Page::where('name', 'Terms & Conditions')->first();
        if($request->isMethod('post')){
            $data = [
              "name" => "Terms & Conditions",
              "Title" => "Terms & Conditions",
              "contents" => $request->body,
            ];

            if($content){
                $content->update($data);
            }else{
                Page::create($data);
            }

            return redirect()->back()->with('success', 'Page content updated successfully!');
        }
        return view('superadmin.pages.terms-conditions', compact('content'));
    }
}
