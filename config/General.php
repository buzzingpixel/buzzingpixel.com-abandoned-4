<?php

declare(strict_types=1);

namespace Config;

class General
{
    /** @var string */
    public static $siteName = 'BuzzingPixel';

    /** @var string */
    public static $twitterHandle = 'buzzingpixel';

    /** @var string[] */
    public static $stylesheets = [
        'https://fonts.googleapis.com/css?family=Arvo:400,400i,700,700i|Noto+Sans+SC:100,300,400,500,700,900',
        // Deployment process will change the filename to something like style.min.1553365271.css
        '/assets/css/style.min.css',
    ];

    /** @var string[] */
    public static $jsFiles = [
        'vue' => 'https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js',
        'main' => [
            'src' => '/assets/js/main.js?v=',
            'type' => 'module',
        ],
    ];

    /**
     * @param mixed[] $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return self::${$name} ?? null;
    }
}
