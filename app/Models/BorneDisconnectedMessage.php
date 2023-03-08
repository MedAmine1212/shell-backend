<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorneDisconnectedMessage
{
    public $type;
    public $borne_id;
    public $station_id;

    public function __construct($type,$station_id, $borne_id)
    {
        $this->type = $type;
        $this->station_id = $station_id;
        $this->borne_id = $borne_id;
    }
}
