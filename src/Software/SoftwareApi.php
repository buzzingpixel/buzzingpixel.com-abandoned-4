<?php

declare(strict_types=1);

namespace App\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\Services\DeleteSoftware;
use App\Software\Services\FetchAllSoftware;
use App\Software\Services\FetchSoftwareBySlug;
use App\Software\Services\SaveSoftware;
use Psr\Container\ContainerInterface;

class SoftwareApi
{
    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveSoftware(SoftwareModel $model) : Payload
    {
        /** @var SaveSoftware $service */
        $service = $this->di->get(SaveSoftware::class);

        return $service($model);
    }

    public function fetchSoftwareBySlug(string $slug) : ?SoftwareModel
    {
        /** @var FetchSoftwareBySlug $service */
        $service = $this->di->get(FetchSoftwareBySlug::class);

        return $service($slug);
    }

    /**
     * @return SoftwareModel[]
     */
    public function fetchAllSoftware() : array
    {
        /** @var FetchAllSoftware $service */
        $service = $this->di->get(FetchAllSoftware::class);

        return $service();
    }

    public function deleteSoftware(SoftwareModel $model) : void
    {
        /** @var DeleteSoftware $service */
        $service = $this->di->get(DeleteSoftware::class);

        $service($model);
    }
}
