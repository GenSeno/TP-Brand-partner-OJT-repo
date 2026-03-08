<?php

namespace App\States\JobOrderState;

use App\Models\Staff;
use App\States\BaseState;
use App\States\Traits\HasSorting;
use Illuminate\Support\Collection;
use Spatie\ModelStates\StateConfig;

/**
 * @extends BaseState<\App\Models\JobOrder>
 */
abstract class JobOrderState extends BaseState
{
    use HasSorting;

    abstract public static function getLabel(): ?string;

    abstract public static function getIcon(): ?string;

    abstract public static function getColor(): ?string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(NewOrder::class)
            ->allowTransition(NewOrder::class, Artist::class)
            ->allowTransition(Artist::class, Approval::class)
            ->allowTransition(Approval::class, Printing::class)
            ->allowTransition(Printing::class, HeatPress::class)
            ->allowTransition(HeatPress::class, Sewing::class)
            ->allowTransition(Sewing::class, Packing::class)
            ->allowTransition(Packing::class, Dispatching::class)
            ->allowTransition(Dispatching::class, Completed::class)

            ->allowTransition(NewOrder::class, Cancelled::class)
            ->allowTransition(Artist::class, Cancelled::class)
            ->allowTransition(Approval::class, Cancelled::class)
            ->allowTransition(Printing::class, Cancelled::class)
            ->allowTransition(HeatPress::class, Cancelled::class)
            ->allowTransition(Sewing::class, Cancelled::class)
            ->allowTransition(Packing::class, Cancelled::class)
            ->allowTransition(Dispatching::class, Cancelled::class);
    }

    public static function getOrder(): array
    {
        return [
            NewOrder::$name,
            Artist::$name,
            Approval::$name,
            Printing::$name,
            HeatPress::$name,
            Sewing::$name,
            Packing::$name,
            Dispatching::$name,
            Completed::$name,
            Cancelled::$name,
        ];
    }

    public static function getPermission(): ?string
    {
        return static::$permission ?? null;
    }

    public static function getNextLabel(): ?string
    {
        $next = static::getNext();
        return $next ? $next::getLabel() : null;
    }

    public static function getPermittedStates(Staff $staff, $except = []): Collection
    {
        return self::allOrdered()->filter(function ($class, $_) use ($staff, $except) {
            return $staff->can($class::getPermission()) && !in_array($class, $except);
        });
    }
}
