<?php

declare(strict_types=1);

namespace Config;

use Config\Abstractions\SimpleModel;

class MainMenu extends SimpleModel
{
    /** @var mixed[] */
    public static $menu = [
        [
            'href' => '#0',
            'content' => 'software',
            'subItems' => [
                [
                    'href' => '/software/ansel-craft',
                    'content' => 'Ansel for Craft',
                ],
                [
                    'href' => '/software/ansel-ee',
                    'content' => 'Ansel for EE',
                ],
                [
                    'href' => '/software/treasury',
                    'content' => 'Treasury',
                ],
                [
                    'href' => '/software/ansel-treasury-ee',
                    'content' => 'Ansel + Treasury Bundle',
                ],
                [
                    'href' => '/software/construct',
                    'content' => 'Construct',
                ],
                [
                    'href' => '/software/category-construct',
                    'content' => 'Category Construct',
                ],
                [
                    'href' => '/software/collective',
                    'content' => 'Collective',
                ],
                [
                    'href' => '/software/marksmin',
                    'content' => 'Marksmin',
                ],
                [
                    'href' => '/software/field-limits',
                    'content' => 'Field Limits',
                ],
                [
                    'href' => '/software/typographee',
                    'content' => 'Typographee',
                ],
            ],
        ],
        [
            'href' => '#0',
            'content' => 'support',
            'subItems' => [
                [
                    'href' => '/support',
                    'content' => 'Support',
                ],
                [
                    'href' => '/support/public',
                    'content' => 'Public Support',
                ],
                [
                    'href' => '/support/private',
                    'content' => 'Private Support',
                ],
            ],
        ],
    ];
}
