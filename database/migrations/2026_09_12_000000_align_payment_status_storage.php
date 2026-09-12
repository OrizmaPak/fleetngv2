<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlignPaymentStatusStorage extends Migration
{
    public function up()
    {
        // The recovered schema used an integer, but both existing payment writers use strings.
        if (DB::getDriverName() === 'mysql') DB::statement("ALTER TABLE trip_payments MODIFY status VARCHAR(24) NOT NULL DEFAULT 'pending'");
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE trip_payments ALTER COLUMN status DROP DEFAULT');
            DB::statement('ALTER TABLE trip_payments ALTER COLUMN status TYPE VARCHAR(24) USING status::text');
            DB::statement("ALTER TABLE trip_payments ALTER COLUMN status SET DEFAULT 'pending'");
        }
        // SQLite already preserves the existing string values despite its integer affinity.
    }

    public function down()
    {
        // Keep the wider type: narrowing would destroy recorded provider statuses.
    }
}
