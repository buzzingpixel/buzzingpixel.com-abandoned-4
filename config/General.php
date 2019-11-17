<?php

declare(strict_types=1);

namespace Config;

use Config\Abstractions\SimpleModel;
use function dirname;
use function getenv;

/**
 * @method bool devMode()
 * @method string pathToContentDirectory()
 * @method string siteName()
 * @method string twitterHandle()
 * @method array stylesheets()
 * @method array jsFiles()
 */
class General extends SimpleModel
{
    public function __construct()
    {
        static::$devMode = getenv('DEV_MODE') === 'true';

        static::$pathToContentDirectory = dirname(__DIR__) . '/content';
    }

    /** @var bool */
    public static $devMode = false;

    /** @var string */
    public static $pathToContentDirectory = '';

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

    /** @var array<string, array<string, string>|string> */
    public static $jsFiles = [
        'vue' => 'https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js',
        'main' => [
            'src' => '/assets/js/main.js?v=',
            'type' => 'module',
        ],
    ];
}
