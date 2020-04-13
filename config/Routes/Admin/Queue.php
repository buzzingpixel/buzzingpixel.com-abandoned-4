<?php

declare(strict_types=1);

use App\Http\Admin\Queue\GetAdminQueueAction;
use App\Http\Admin\Queue\PostAdminQueueAction;
use Config\NoOp;
use Slim\Routing\RouteCollectorProxy;

return static function (RouteCollectorProxy $r) : void {
    $r->group('/queue', function (RouteCollectorProxy $r) : void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->get('', GetAdminQueueAction::class);

        $r->post('', PostAdminQueueAction::class);
    });
};
