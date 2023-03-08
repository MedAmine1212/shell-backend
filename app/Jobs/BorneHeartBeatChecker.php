<?php

namespace App\Jobs;

use App\Events\MessageEvent;
use App\Events\StationAdminEvent;
use App\Events\SuperAdminEvent;
use App\Models\Borne;
use App\Models\BorneDisconnectedMessage;
use App\Models\WebSocketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Pusher\PusherException;

class BorneHeartBeatChecker implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */


    protected $id;
    protected $heartBeatInterval;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $heartBeatInterval)
    {
        $this->id = $id;
        $this->heartBeatInterval = $heartBeatInterval;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws PusherException
     */
    public function handle()
    {
        $borne = Borne::where("id", $this->id)->get()->first();
        if(!$borne)
            return;

        $now =date("Y-m-d H:i:s",strtotime('+1 hour'));
        if(strtotime($now)*1000-strtotime($borne->lastHeartBeat)*1000 > $this->heartBeatInterval) {
        $borne->status = false;
        $borne->update();
            event(new SuperAdminEvent(new BorneDisconnectedMessage("borneDisconnected",$borne->station_id,$this->id)));
            event(new StationAdminEvent(new BorneDisconnectedMessage("borneDisconnected",$borne->station_id,$this->id)));
            error_log("Borne disconnected");
        } else {
            error_log("Queue updated");
            BorneHeartBeatChecker::dispatch($this->id, $borne->heartBeatInterval)
                ->delay(now()->addMinutes($borne->heartBeatInterval))
                ->onConnection('database');
        }
    }
}
