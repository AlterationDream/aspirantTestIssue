<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PostController;

class ImportPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:import {amount=10 : Amount of posts to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import iTunes Movie Trailers into database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $amount = $this->argument('amount');
        if (!is_numeric($amount)) {
            $this->error('The amount of posts should be an integer!');
            return Command::INVALID;
        }
        if ($amount < 1 || $amount > 10) {
            $this->error('The amount of posts should lie within range of 1 to 10');
            return Command::INVALID;
        }
        $fresh = $this->confirm('Delete any previous DB post records?', false);

        $response = (new \App\Http\Controllers\PostController)->import($amount, $fresh);

        if ($response['status'] == 'error') {
            $this->error($response['error']);
            return Command::FAILURE;
        }

        $this->info('Posts were successfully imported!');
        return Command::SUCCESS;
    }
}
