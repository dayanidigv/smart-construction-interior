<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropAllTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop-all-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables from the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Dropping all tables...');

        $tables = DB::select('SHOW TABLES');

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            $tableArray = (array)$table;
            $tableName = array_values($tableArray)[0];
            Schema::drop($tableName);
            $this->info("Dropped: {$tableName}");
        }

        Schema::enableForeignKeyConstraints();

        $this->info('All tables dropped successfully!');

        return 0;
    }
}
