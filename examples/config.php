<?php

return [
    'core_events' => [
        'is_active' => false,
        'constants' => [
            // bool
            'W4WP_DISABLE_LOGIN' => false,
            // bool
            'W4WP_ALLOW_REDIRECT' => false,
            // bool
            'W4WP_DISABLE_REST_API' => false,
            // bool
            'W4WP_ONLY_OEMBED' => false,
            // bool
            'W4WP_MSNBOT' => false,
            // bool
            'W4WP_GOOGLEBOT' => false,
            // bool
            'W4WP_YANDEXBOT' => false,
            // bool
            'W4WP_GOOGLEPROXY' => false,
            // bool
            'W4WP_SEZNAMBOT' => false,
            // bool
            'W4WP_CONTENTKING' => false,
            // bool
            'W4WP_FACEBOOKCRAWLER' => false,
        ],
    ],
    'http_analyzer' => [
        'is_active' => false,
        'constants' => [
            // bool
            'W4WP_INSTANT' => false,
            // string
            // W4WP_PROXY_HOME_URL should not have a trailing slash.
            'W4WP_PROXY_HOME_URL' => '',
            // int
            'W4WP_MAX_LOGIN_REQUEST_SIZE' => 120,
            // string
            // `:` seperated.
            'W4WP_CDN_HEADERS' => '',
            // bool
            'W4WP_ALLOW_REG' => false,
            // bool
            'W4WP_ALLOW_IE8' => false,
            // bool
            'W4WP_ALLOW_OLD_PROXIES' => false,
            // bool
            'W4WP_ALLOW_CONNECTION_EMPTY' => false,
            // bool
            'W4WP_ALLOW_CONNECTION_CLOSE' => false,
            // bool
            'W4WP_ALLOW_IE_NO_REFERER' => false,
            // bool
            'W4WP_ALLOW_TWO_CAPS' => false,
            // bool
            'W4WP_DISALLOW_TOR_LOGIN' => false,
            // bool
            'W4WP_POST_LOGGING' => false,
        ]
    ],
];
