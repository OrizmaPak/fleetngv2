<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\RawMessageFromArray;

class Driver extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'photo',
        'auth_pin',
        'last_active',
        'user_type',
        'vehicle_id',
        'user_id',
        'device_serial_number',
        'voice_number',
        'billing_term',
        'is_active',
        'merchant_id',
        'driver_id',
        'device_token',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */

    //  Determine full name of user
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function saveDeviceToken(string $token = "")
    {
        if ($token) {
            $this->device_token = $token;
            $this->save();

            $record = DeviceToken::where(['token' => $token, 'driver_id' => $this->id])->first();
            if ($record) {
                $record->last_activity = now();
                $record->save();
            } else {
                DeviceToken::create(['token' => $token, 'driver_id' => $this->id]);
            }
        }
    }

    public function removeDeviceToken($token = "")
    {
        if ($token) {
            DeviceToken::where(['token' => $token, 'driver_id' => $this->id])->delete();
        }
        $this->device_token = "";
        $this->save();
    }

    public function sendPushNotification()
    {
        $firebase = (new Factory)
            ->withServiceAccount($this->firebaseCredentials());

        $messaging = $firebase->createMessaging();
        $device_token = $this->device_token;

        $message = CloudMessage::fromArray([
            "token" => $device_token,
            'notification' => [
                'title' => 'Hello from Firebase!',
                'body' => 'This is a test notification.'
            ],
        ]);

        $messageId = $messaging->send($message);

        return response()->json(['message' => 'Push notification sent successfully', 'id' => $messageId]);
    }

    public function sendPushNotification2()
    {
        $firebase = (new Factory)
            ->withServiceAccount($this->firebaseCredentials());

        $messaging = $firebase->createMessaging();
        $device_token = $this->device_token;
        $notification = [
            'title' => 'Hello from Firebase!',
            'body' => 'This is a test notification.'
        ];

        $message = new RawMessageFromArray([
            "token" => $device_token,
            'notification' => $notification,
            'android' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
                'notification' => [
                    'title' => 'Android Title',
                    'body' => 'Android Body',
                ],
            ],
            'apns' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => 'iOS Title',
                            'body' => 'iOS Body',
                        ],
                    ],
                ],
            ],
            'webpush' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
                'notification' => [
                    'title' => 'Webpush Title',
                    'body' => 'Webpush Body'
                ],
            ]
        ]);


        $messageId = $messaging->send($message);

        return response()->json(['message' => 'Push notification sent successfully', 'id' => $messageId]);
    }

    public function sendPushNotification3()
    {
        $firebase = (new Factory)
            ->withServiceAccount($this->firebaseCredentials());

        $messaging = $firebase->createMessaging();
        $device_token = [$this->device_token];

        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => 'Hello from Firebase!',
                'body' => 'This is a test notification.'
            ],
        ]);

        $report = $messaging->sendMulticast($message, $device_token);

        return response()->json([
            'message' => 'Push notification sent successfully',
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count(),
        ]);
    }

    private function firebaseCredentials(): string
    {
        $credentials = config('services.firebase.credentials');

        if (!$credentials || !is_file($credentials)) {
            throw new \RuntimeException('FIREBASE_CREDENTIALS must point to a readable service-account JSON file.');
        }

        return $credentials;
    }
}
