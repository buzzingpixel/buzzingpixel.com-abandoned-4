<?php

declare(strict_types=1);

namespace Config;

use Config\Abstractions\SimpleModel;

class Footer extends SimpleModel
{
    /** @var array<int, array<string, string>> */
    public static $menu = [
        [
            'href' => '/cookies',
            'content' => 'Cookie Policy',
        ],
        [
            'href' => '/privacy',
            'content' => 'Privacy Policy',
        ],
        [
            'href' => '/terms',
            'content' => 'Terms of Service',
        ],
    ];
}
