<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Traits\RoomsTrait;

class ProcessRoom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use RoomsTrait;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $room_id;
    private $room_no;
    private $room;

    public function __construct($room_id, $room_no=null)
    {
        $this->room_id = $room_id;
        $this->room_no = $room_no;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->entrance($this->room_id, $this->room_no);
    }
}
