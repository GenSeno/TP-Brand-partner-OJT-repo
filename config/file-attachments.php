<?php

return [

    'allowed_mime_types' => [

        'default' => [
            'image/*',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ],

    ],

    'allowed_extensions' => [

        'default' => [
            'jpg',
            'jpeg',
            'png',
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'txt',
        ],

    ],

    'max' => [
        'default' => 25600, // in KB
    ],

];
