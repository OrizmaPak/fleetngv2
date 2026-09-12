<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\Customers\Entities\Customer;
use Tests\TestCase;

class CustomerPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_photo_upload_validation_and_private_delivery()
    {
        Storage::fake('local');
        config(['customers.profile_disk'=>'local']);
        $customer = Customer::create(['first_name'=>'Photo','email'=>'photo@example.test','phone_number'=>'8000000001','is_active'=>1]);
        Sanctum::actingAs($customer);
        $data = ['first_name'=>'Photo','email'=>$customer->email,'phone_number'=>$customer->phone_number];
        $data['profile_image'] = UploadedFile::fake()->createWithContent('photo.png',base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l9sAAAAASUVORK5CYII='));
        $this->postJson('/api/customer/profile',$data)->assertOk();
        $path = $customer->fresh()->profile_image;
        Storage::disk('local')->assertExists($path);
        $this->get('/api/customer/profile-image')->assertOk()->assertHeader('X-Content-Type-Options','nosniff');
        $data['profile_image'] = UploadedFile::fake()->createWithContent('photo.svg','<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $this->postJson('/api/customer/profile',$data)->assertStatus(400);
        $this->assertSame($path,$customer->fresh()->profile_image);
    }
}
