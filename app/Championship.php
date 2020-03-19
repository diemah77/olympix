<?php

namespace App;

use App\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{
    use HasStatus;

    const DOUBLES_DRAW_RANDOM = 1;
    const DOUBLES_DRAW_TTR = 2;
    const DOUBLES_DRAW_POOLS = 3;

    const SETS_FIVE = 5;
    const SETS_SEVEN = 7;

    protected $casts = [
        'third_place' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($championship)
        {
            $championship->tournament->increment('championships_count');

            $p2 = KoPhase::create();
            $p2->phase()->create([
                'championship_id'=> $championship->id,
                'order' => 2,
                'draw_type' => Phase::DRAW_TYPE_TTR
            ]);

            if ($championship->isGroupElimination())
            {
                $group = GroupPhase::create();
                $p1 = $group->phase()->create([
                    'championship_id'=> $championship->id,
                    'draw_type' => Phase::DRAW_TYPE_TTR,
                    'order' => 1
                ]);

                $p1->nextPhase()->associate($p2)->save();
            }
        });
    }

    public function system()
    {
        return $this->belongsTo(ChampionshipSystem::class, 'system_id');
    }

    public function type()
    {
        return $this->belongsTo(ChampionshipType::class, 'type_id');
    }

    public function doublesDraw()
    {
        return [
            ['id' => self::DOUBLES_DRAW_RANDOM, 'label' => 'Zufällig'],
            ['id' => self::DOUBLES_DRAW_TTR, 'label' => 'Nach Spielstärke'],
            ['id' => self::DOUBLES_DRAW_POOLS, 'label' => '2 Lostöpfe'],
        ];
    }

    public function doublesDrawName()
    {
        return isset($this->doublesDrawList()[$this->doubles_draw_type]) ? $this->doublesDrawList()[$this->doubles_draw_type] : $this->doubles_draw_type;
    }

    public function isDoubles()
    {
        return $this->type->isDoubles();
    }

    public function isSingles()
    {
        return $this->type->isSingles();
    }

    public function isDoubleElimination()
    {
        return $this->system->name == 'Doppel-KO';
    }

    public function isGroupElimination()
    {
        return $this->system->name == 'Gruppen/KO';
    }

    public function setsList()
    {
        return [
            ['id' => self::SETS_FIVE, 'label' => 'Best-of-5'],
            ['id' => self::SETS_SEVEN, 'label' => 'Best-of-7'],
        ];
    }

    public function winningSets()
    {
        return ($this->sets + 1) / 2;
    }

    public function tournament()
    {
    	return $this->belongsTo(Tournament::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function doubles()
    {
        return $this->participants()->where('participantable_type', 'App\Double');
    }

    public function getByeParticipant()
    {
        return $this->participants()->create(['bye' => true])->fresh();
    }

    public function registrations()
    {
    	return $this->hasMany(Registration::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'registrations');
    }

    public function phases()
    {
        return $this->hasMany(Phase::class);
    }

    public function results()
    {
        return Result::size($this->sets)->get();
    }

    public function reversed()
    {
        return Result::size($this->sets)->get()->map(function ($result)
        {
            $result->name = $result->reversed_name;

            return $result;
        });
    }

    public function allPhasesFinished()
    {
        return $this->phases->filter(function($phase)
        {
            return !$phase->isFinished();
        })
        ->count() == 0;
    }

    public function hasStartedPhase()
    {
        if ($this->phases->count() == 0) return false;

        return $this->phases->filter(function($phase)
        {
            return $phase->isStarted();
        })
        ->count() > 0;
    }

    public function hasNonPreparedPhase()
    {
        if ($this->phases->count() == 0) return false;

        return $this->phases->filter(function($phase)
        {
            return !$phase->isPrepared();
        })
        ->count() > 0;
    }

    public function updateStatus()
    {
        if ($this->allPhasesFinished())
        {
            $this->update(['status' => self::$FINISHED]);
            $this->tournament->updateStatus();

            return;
        }

        if ($this->hasStartedPhase())
        {
            $this->update(['status' => self::$STARTED]);
            $this->tournament->updateStatus();

            return;
        }
    }

    // Ermittle die relevanten Teilnehmer für die Auslosung der Phase
    //
    public function getAvailableParticipants($phase)
    {
        if (is_null($phase->previousPhase))
        {
            return $this->participants()->with(['participantable'])
                ->get()
                ->sortByDesc(function($p)
                {
                    return $p->ttr();
                });
            list($seeded, $rest) = $this->participants()->with(['participantable'])
                ->get()
                ->sortByDesc(function($p)
                {
                    return $p->ttr();
                })
                ->partition(function($p)
                {
                    return $p->seed > 0;
                });

            return $seeded->sortBy('seed')->concat($rest);
        }
        else
        {
            if ($phase->previousPhase->isFinished())
            {
                // Wenn die Phase bereits zurückgesetzt wurde, fehlen die Qualifizierten
                // Also: Qualifizierte nochmals ersetzen und zuweisen
                if ($phase->participants->count() == 0)
                {
                    $phase->previousPhase->phaseable->replaceAvailables();

                    // return $phase->championship->participants()->withoutGlobalScope('regular')->available()->get();
                }

                return $phase->participants()->get();
            }

            return $this->participants()->available()->get();
        }
    }

    public function register($player)
    {
        $registration = $this->registrations()->create(['player_id' => $player->id]);

        if ($this->isSingles()) $this->addSingle($registration);

        return $registration;
    }

    public function isPlayerRegistered($player)
    {
        if ($this->registrations->count() == 0)
        {
            return false;
        }

        return $this->registrations->contains('player_id', $player->id);
    }

    public function addSingle($registration)
    {
        $single = $registration->single()->create();

        $this->addParticipant($single);
    }

    public function addDouble($registration1, $registration2)
    {
        $double = Double::create(['registration1_id' => $registration1->id, 'registration2_id' => $registration2->id]);

        $this->addParticipant($double);
    }

    public function addParticipant($competitor, $available = false)
    {
        $participant = $this->participants()->create(['available' => $available]);
        $participant->participantable()->associate($competitor)->save();
    }

    public function createThirdPlaceGame($round)
    {
        return $this->third_place && $round->isSemi();
    }
}
