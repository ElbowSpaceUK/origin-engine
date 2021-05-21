<?php

return [
    'default' => 'config',
    // TODO Move to the symfony component
    'disks' => [
        'config' => [
            'driver' => 'local',
            'root' => $_SERVER['HOME'] . '/.atlas-cli'
        ]
    ]
];
