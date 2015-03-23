<?php

return [
    'predis.parameters' => 'tcp://127.0.0.1:6379',
    'predis.options'    => [
        'prefix'  => 'cf:',
        'profile' => '3.0',
    ],

    'config.numberOfRecentTrades' => 10,

    'config.zeromq.dsn' => "tcp://127.0.0.1:5555",
    'config.zeromq.persistent_id' => 'trade messages',

    'config.totalsDataExpiryPeriod' => 7 // how long to keep totals data
];