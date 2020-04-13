<?php

namespace App\Rules;

use App\Result;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class ValidMatchSets implements Rule
{
    protected $result;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($resultId)
    {
        $this->result = Result::find($resultId);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_null($this->result))
        {
            return false;
        }

        $sets = collect($value);

        $filled = $sets->filter(function ($set)
        {
            return is_numeric($set['points']);
        });

        if ($filled->count() != $this->result->setCount())
        {
            // dd('filled', $filled);
            return false;
        }

        // TODO: collection->partition
        list($positive, $negative) = $sets->partition(function($set)
        {
            return !Str::startsWith($set['points'], '-');
        });

        if ($positive->count() != $this->result->left)
        {
            return false;
        }

        if ($negative->count() != $this->result->right)
        {
            return false;
        }

        // Bei Sieg von p1 muss der letzte Satz zu seinen Gunsten sein
        if ($this->result->left > $this->result->right)
        {
            if (intval($sets->last()['points']) < 0)
            {
                return false;
            }
        }

        // Bei Sieg von p2 muss der letzte Satz zu seinen Gunsten sein
        if ($this->result->right > $this->result->left)
        {
            if (intval($sets->last()['points']) > 0)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Bitte die Sätze korrekt eingeben!';
    }
}
