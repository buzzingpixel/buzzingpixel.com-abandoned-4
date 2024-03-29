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
 * @method string pathToStorageDirectory()
 * @method string pathToSecureStorageDirectory()
 * @method string siteUrl()
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

        static::$pathToStorageDirectory = $rootPath . '/storage';

        static::$pathToSecureStorageDirectory = $rootPath . '/secure-storage';

        if (getenv('SITE_URL') !== false) {
            static::$siteUrl = getenv('SITE_URL');
        }

        if (
            ! static::$devMode ||
            getenv('USE_DYNAMIC_SITE_URL') !== 'true' ||
            ! isset($_SERVER['HTTP_HOST'])
        ) {
            return;
        }

        static::$siteUrl = 'https://' . $_SERVER['HTTP_HOST'];
    }

    public static bool $devMode = false;

    public static string $rootPath = '';

    public static string $pathToContentDirectory = '';

    public static string $pathToStorageDirectory = '';

    public static string $pathToSecureStorageDirectory = '';

    public static string $siteUrl = 'https://www.buzzingpixel.com';

    public static string $siteName = 'BuzzingPixel';

    public static string $twitterHandle = 'buzzingpixel';

    /** @var string[] */
    public static array $stylesheets = ['https://rsms.me/inter/inter.css'];

    /** @var string[] */
    public static array $jsFiles = ['https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js'];
}
