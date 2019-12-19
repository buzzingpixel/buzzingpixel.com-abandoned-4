<?php

declare(strict_types=1);

namespace Config;

use Config\Abstractions\SimpleModel;
use function dirname;
use function getenv;

/**
 * @method bool devMode()
 * @method string rootPath()
 * @method string pathToContentDirectory()
 * @method string pathToSecureStorageDirectory()
 * @method string siteName()
 * @method string twitterHandle()
 * @method array stylesheets()
 * @method array jsFiles()
 */
class General extends SimpleModel
{
    public function __construct()
    {
        $rootPath = dirname(__DIR__);

        static::$devMode = getenv('DEV_MODE') === 'true';

        static::$rootPath = $rootPath;

        static::$pathToContentDirectory = $rootPath . '/content';

        static::$pathToSecureStorageDirectory = $rootPath . '/secure-storage';
    }

    /** @var bool */
    public static $devMode = false;

    /** @var string */
    public static $rootPath = '';

    /** @var string */
    public static $pathToContentDirectory = '';

    /** @var string */
    public static $pathToSecureStorageDirectory = '';

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
