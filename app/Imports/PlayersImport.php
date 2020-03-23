<?php

namespace App\Imports;

use App\Tournament;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class PlayersImport implements OnEachRow
{
    protected $tournament;

    public function __construct(Tournament $tournament)
    {
        $this->tournament = $tournament;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        if (trim($row[0]) && trim($row[1]) && is_int($row[2]))
        {
            $this->tournament->players()->updateOrCreate(
                ['firstname' => $row[0], 'lastname' => $row[1]],
                ['ttr' => $row[2]]
            );
        }
    }
}
