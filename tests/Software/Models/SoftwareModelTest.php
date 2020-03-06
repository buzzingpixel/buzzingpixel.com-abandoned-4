<?php

declare(strict_types=1);

namespace Tests\Software\Models;

use App\Software\Models\SoftwareModel;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SoftwareModelTest extends TestCase
{
    public function testSetInvalidProperty() : void
    {
        $software = new SoftwareModel();

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Invalid property');

        $software->asdf = 'thing';
    }

    public function testGetInvalidProperty() : void
    {
        $software = new SoftwareModel();

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Invalid property');

        $software->asdf;
    }

    public function testIsset() : void
    {
        $software = new SoftwareModel();

        self::assertFalse(isset($software->asdf));

        self::assertTrue(isset($software->versions));
    }
}
