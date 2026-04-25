<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = \'public\'');
$search = 'Minuman';

foreach ($tables as $table) {
    $tableName = $table->tablename;
    $columns = Schema::getColumnListing($tableName);
    
    foreach ($columns as $column) {
        $count = DB::table($tableName)->where($column, 'like', '%' . $search . '%')->count();
        if ($count > 0) {
            echo "Found in $tableName.$column: $count records\n";
        }
    }
}
