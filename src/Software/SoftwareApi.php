<?php

declare(strict_types=1);

namespace App\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\DeleteSoftware;
use App\Software\Services\DeleteSoftwareVersion;
use App\Software\Services\FetchAllSoftware;
use App\Software\Services\FetchSoftwareById;
use App\Software\Services\FetchSoftwareBySlug;
use App\Software\Services\FetchSoftwareVersionById;
use App\Software\Services\SaveSoftware;
use Psr\Container\ContainerInterface;
use function assert;

class SoftwareApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function saveSoftware(SoftwareModel $model) : Payload
    {
        $service = $this->di->get(SaveSoftware::class);
        assert($service instanceof SaveSoftware);

        return $service($model);
    }

    public function fetchSoftwareById(string $id) : ?SoftwareModel
    {
        $service = $this->di->get(FetchSoftwareById::class);
        assert($service instanceof FetchSoftwareById);

        return $service($id);
    }

    public function fetchSoftwareBySlug(string $slug) : ?SoftwareModel
    {
        $service = $this->di->get(FetchSoftwareBySlug::class);
        assert($service instanceof FetchSoftwareBySlug);

        return $service($slug);
    }

    /**
     * @return SoftwareModel[]
     */
    public function fetchAllSoftware() : array
    {
        $service = $this->di->get(FetchAllSoftware::class);
        assert($service instanceof FetchAllSoftware);

        return $service();
    }

    public function fetchSoftwareVersionById(string $id) : ?SoftwareVersionModel
    {
        $service = $this->di->get(FetchSoftwareVersionById::class);
        assert($service instanceof FetchSoftwareVersionById);

        return $service($id);
    }

    public function deleteSoftware(SoftwareModel $model) : void
    {
        $service = $this->di->get(DeleteSoftware::class);
        assert($service instanceof DeleteSoftware);

        $service($model);
    }

    public function deleteSoftwareVersion(SoftwareVersionModel $model) : void
    {
        $service = $this->di->get(DeleteSoftwareVersion::class);
        assert($service instanceof DeleteSoftwareVersion);

        $service($model);
    }
}
