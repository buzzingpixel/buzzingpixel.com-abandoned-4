<?php

declare(strict_types=1);

use App\Http\StandardPages\StandardPageAction;
use Slim\App;

return static function (App $app): void {
    $app->get('/', StandardPageAction::class);
};
