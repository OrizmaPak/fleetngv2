<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RecoveredSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function testRecoveredDomainSchemaIsAvailable()
    {
        foreach (['customers', 'device_tokens', 'expenses', 'pages', 'sms_logs', 'trip_payments', 'trip_requests', 'draft_trips'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing recovered table: {$table}");
        }

        $this->assertTrue(Schema::hasColumns('trips', [
            'cost_of_sand',
            'road_money',
            'pick_up_datetime',
            'payment_id',
            'client_id',
        ]));
    }
}
