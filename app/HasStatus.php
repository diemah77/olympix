<?php

namespace App;

trait HasStatus
{
	public static $PREPARED = 1;
	public static $STARTED = 2;
	public static $FINISHED = 3;

	public function statusName()
	{
		$states = [
            self::$PREPARED => 'Vorbereitet',
            self::$STARTED => 'Gestartet',
            self::$FINISHED => 'Beendet'
        ];

		return isset($states[$this->status]) ? $states[$this->status] : $this->status;
	}
}
