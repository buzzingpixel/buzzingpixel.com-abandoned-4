<?php

declare(strict_types=1);

namespace App\Users\Models;

/**
 * Exists for auto-wiring logged in user to __constructor injection
 * of classes that require user to be logged in
 */
class LoggedInUser
{
    private UserModel $model;

    public function __construct(UserModel $model)
    {
        $this->model = $model;
    }

    public function model() : UserModel
    {
        return $this->model;
    }
}
