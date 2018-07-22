<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InitializeDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(file_get_contents(storage_path(config('quick_migrations.dump_file'))));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
