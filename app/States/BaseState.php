<?php

namespace App\States;

use Spatie\ModelStates\State;

abstract class BaseState extends State
{
    abstract public static function getNext(): ?string;

    public function transitionToNext()
    {
        $next = static::getNext();

        if ($next) {
            $this->transitionTo($next);
        } else {
            throw new \Exception('No next stage defined.');
        }
    }

    public function is(string|State $state): bool
    {
        return $this::class === $state || $this instanceof $state;
    }

    public function in(string|State ...$state): bool
    {
        return in_array($this::class, $state, true);
    }
}
