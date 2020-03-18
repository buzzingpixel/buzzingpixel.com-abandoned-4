<?php

declare(strict_types=1);

namespace App\Orders;

use App\Orders\Models\OrderModel;
use App\Orders\Services\Fetch\FetchUsersOrdersMaster;
use App\Orders\Services\SaveOrder\SaveOrderMaster;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use Psr\Container\ContainerInterface;
use function assert;

class OrderApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveOrder(OrderModel $orderModel) : Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveOrderMaster::class);

        assert($service instanceof SaveOrderMaster);

        return $service($orderModel);
    }

    /**
     * @return OrderModel[]
     */
    public function fetchUsersOrders(UserModel $user) : array
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchUsersOrdersMaster::class);

        assert($service instanceof FetchUsersOrdersMaster);

        return $service($user);
    }

    /**
     * @return OrderModel[]
     */
    public function fetchCurrentUserOrders() : array
    {
        /** @psalm-suppress MixedAssignment */
        $userApi = $this->di->get(UserApi::class);

        assert($userApi instanceof UserApi);

        $user = $userApi->fetchLoggedInUser();

        assert($user instanceof UserModel);

        /** @psalm-suppress PossiblyNullArgument */
        return $this->fetchUsersOrders($user);
    }
}
