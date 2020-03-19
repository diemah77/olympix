<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
	public function scopeSize($query, $size)
	{
		return $query->whereSize($size);
	}

	public function label()
	{
		return "{$this->left}:{$this->right}";
    }

    public function reversedLabel()
	{
		return "{$this->right}:{$this->left}";
	}

	public function setCount()
	{
		return $this->left + $this->right;
	}
}
