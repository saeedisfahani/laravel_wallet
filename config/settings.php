<?php

return array(
    'apis' => array(
        'navasan' => array(
            'url' => 'http://api.navasan.tech/latest/?api_key=',
            'key' => env('NAVASAN_API_KEY')
        )
    ),
    'pagination' => array(
        'items_per_page' => 1
    ),
    'payments' => array(
        'payment_creation_time_limit' => 10,
        'delete_deprecated_payment_after_hours' => 24,
        'payments_should_delete_count' => 10,
    ),
);
