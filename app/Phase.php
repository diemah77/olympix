<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    const DRAW_TYPE_RANDOM = 1;
    const DRAW_TYPE_TTR = 2;

    const STATUS_PREPARED = 1;
    const STATUS_STARTED = 2;
    const STATUS_FINISHED = 3;

    protected $casts = [
        'draw_type' => 'integer',
        'status' => 'integer',
        'order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($phase)
        {
            if (! is_null($phaseable = $phase->phaseable))
            {
                $phaseable->reset();
                $phaseable->delete();
            }
        });
    }

    public function nextPhase()
    {
        return $this->belongsTo(Phase::class, 'next_phase_id', 'id');
    }

    public function previousPhase()
    {
        return $this->belongsTo(Phase::class, 'id', 'next_phase_id');
    }

    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class);
    }

    public function drawTypeList()
    {
        return [
            ['id' => self::DRAW_TYPE_RANDOM, 'label' => 'Zufällig'],
            ['id' => self::DRAW_TYPE_TTR, 'label' => 'Nach Spielstärke'],
        ];
    }

    public function drawTypeName()
    {
        return isset($this->drawTypeList()[$this->draw_type]) ? $this->drawTypeList()[$this->draw_type] : $this->draw_type;
    }

    public function isDrawRandom()
    {
        return $this->draw_type == self::DRAW_TYPE_RANDOM;
    }

    public function isDrawn()
    {
        return !is_null($this->status);
    }

    public function canBeDrawn()
    {
        if (! $this->previousPhase) return true;

        return $this->previousPhase->isFinished();
    }

    public function canBeStarted()
    {
        if ($this->isDrawn() && !$this->isFinished())
        {
            return $this->previousPhase ? $this->previousPhase->isFinished() : true;
        }

        return false;
    }

    public function isFinished()
    {
        return $this->status == self::STATUS_FINISHED;
    }

    public function phaseable()
    {
        return $this->morphTo();
    }

    public function matches()
    {
        return $this->phaseable->matches();
    }

    public function groupPhase()
    {
        return $this->isGroup() ? $this->phaseable : null;
    }

    public function koPhase()
    {
        return $this->isKo() ? $this->phaseable : null;
    }

    public function isGroup()
    {
        return $this->phaseable instanceOf GroupPhase;
    }

    public function isKo()
    {
        return $this->phaseable instanceOf KoPhase;
    }

    public function type()
    {
        return $this->isKo() ? 'ko' : 'group';
    }

    public function name()
    {
        return $this->isKo() ? 'KO-Phase' : 'Gruppen-Phase';
    }

    public function isStarted()
    {
        return $this->status == self::STATUS_STARTED;
    }

    public function isPrepared()
    {
        return $this->status == self::STATUS_PREPARED;
    }

    public function statusName()
    {
        $arr = [
            self::STATUS_PREPARED => 'Vorbereitet',
            self::STATUS_STARTED => 'Gestartet',
            self::STATUS_FINISHED => 'Beendet',
        ];

        return isset($arr[$this->status]) ? $arr[$this->status] : $this->status;
    }

    public function finish()
    {
        $this->update(['status' => self::STATUS_FINISHED]);

        $this->championship->updateStatus();
    }
}
