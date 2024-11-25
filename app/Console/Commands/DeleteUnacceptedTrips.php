<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Trip;
use Illuminate\Console\Command;


class DeleteUnacceptedTrips extends Command
{
    protected $signature = 'trips:delete-unaccepted';
    protected $description = 'Delete trips not accepted within 30 minutes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $threshold = Carbon::now()->subMinutes(30);
        $deletedTrips = Trip::where('status', 'Pending')
                            ->where('created_at', '<', $threshold)
                            ->delete();

        $this->info("Deleted $deletedTrips unaccepted trips.");
    }
}