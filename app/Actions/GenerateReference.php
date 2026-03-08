<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class GenerateReference
{
    use AsAction;

    public function handle($id, string $config = 'generator.default.reference_format')
    {
        $config = config($config, []);

        $reference = str_pad(
            $id,
            $config['length'] ?? 4,
            $config['padding_character'] ?? 0,
            $config['padding_direction'] ?? STR_PAD_LEFT
        );

        $prefix = $config['prefix'] ?? '';

        return "{$prefix}{$reference}";
    }
}
