<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Reference Format
    |--------------------------------------------------------------------------
    |
    | Specify the format for the default reference generator to use.
    |
    */
    'default' => [

        'reference_format' => [

            'prefix' => date('y') . '-',

            'padding_direction' => STR_PAD_LEFT,

            'padding_character' => '0',

            'length' => 4,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Reference Format (Billing)
    |--------------------------------------------------------------------------
    |
    | Specify the format for the invoice reference generator to use.
    |
    */
    'invoice' => [

        'reference_format' => [

            'prefix' => 'BL' . date('y'),

            'padding_direction' => STR_PAD_LEFT,

            'padding_character' => '0',

            'length' => 4,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Job Order Reference Format
    |--------------------------------------------------------------------------
    |
    | Specify the format for the job order reference generator to use.
    |
    */
    'job_order' => [

        'reference_format' => [

            'prefix' => 'JO-',

            'padding_direction' => STR_PAD_LEFT,

            'padding_character' => '0',

            'length' => 4,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Quotation Reference Format
    |--------------------------------------------------------------------------
    |
    | Specify the format for the quotation reference generator to use.
    |
    */
    'quote' => [

        'reference_format' => [

            'prefix' => 'QU-',

            'padding_direction' => STR_PAD_LEFT,

            'padding_character' => '0',

            'length' => 4,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Sales Order Reference Format
    |--------------------------------------------------------------------------
    |
    | Specify the format for the SO reference generator to use.
    |
    */
    'sales_order' => [

        'reference_format' => [

            'prefix' => 'SO-',

            'padding_direction' => STR_PAD_LEFT,

            'padding_character' => '0',

            'length' => 4,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Purchase Order Reference Format
    |--------------------------------------------------------------------------
    |
    | Specify the format for the PO reference generator to use.
    |
    */
    'purchase_order' => [

        'reference_format' => [

            'prefix' => 'PO' . date('y') . '-',

            'padding_direction' => STR_PAD_LEFT,

            'padding_character' => '0',

            'length' => 4,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Reference Format
    |--------------------------------------------------------------------------
    |
    | Specify the format for the transaction reference generator to use.
    |
    */
    'transaction' => [

        'gcash' => [
            'intent' => [
                'reference_format' => [
                    'prefix' => 'GCASH-INT-' . date('y'),
                    'padding_direction' => STR_PAD_LEFT,
                    'padding_character' => '0',
                    'length' => 6,
                ],

            ],

            'refund' => [
                'reference_format' => [
                    'prefix' => 'GCASH-REF-' . date('y'),
                    'padding_direction' => STR_PAD_LEFT,
                    'padding_character' => '0',
                    'length' => 6,
                ],

            ],

            'capture' => [
                'reference_format' => [
                    'prefix' => 'GCASH-CAP-' . date('y'),
                    'padding_direction' => STR_PAD_LEFT,
                    'padding_character' => '0',
                    'length' => 6,
                ],

            ],
        ]

    ],


    /*
    |--------------------------------------------------------------------------
    | Expense Reference Format
    |--------------------------------------------------------------------------
    |
    | Specify the format for the expense reference generator to use.
    |
    */
    'expense' => [

        'reference_format' => [

            'prefix' => 'PV' . date('y'),

            'padding_direction' => STR_PAD_LEFT,

            'padding_character' => '0',

            'length' => 4,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Reference Format
    |--------------------------------------------------------------------------
    |
    | Specify the format for the payment reference generator to use.
    |
    */
    'payment' => [

        'reference_format' => [

            'prefix' => 'PA-' . date('y'),

            'padding_direction' => STR_PAD_LEFT,

            'padding_character' => '0',

            'length' => 4,
        ],

    ],

];
