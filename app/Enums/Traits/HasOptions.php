<?php

namespace App\Enums\Traits;

trait HasOptions
{
    abstract public function getLabel(): ?string;

    public static function getOptions($keyed = true, $cases = 'cases', ...$args): array
    {
        return collect(self::{$cases}(...$args))
            ->when($keyed, fn($c) => $c->mapWithKeys(fn($case) => [$case->value => $case->getLabel()]))
            ->unless($keyed, fn($c) => $c->map(fn($case) => [
                'label' => $case->getLabel(),
                'value' => $case->value,
            ]))
            ->all();

    }

    public function is($case): bool
    {
        return $this === $case;
    }

    public function in(...$cases): bool
    {
        return in_array($this, $cases, true);
    }
}
