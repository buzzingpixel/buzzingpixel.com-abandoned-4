<?php

declare(strict_types=1);

namespace Tests\App\Content\Documentation;

use App\Content\Documentation\CodeblockPayload;
use App\Content\Documentation\CollectDocumentationPagePayloadFromPath;
use App\Content\Documentation\CollectDocumentationPageSectionFromPath;
use App\Content\Documentation\CollectDocumentationVersionPayloadFromPath;
use App\Content\Documentation\CollectDocumentationVersionsFromPath;
use App\Content\Documentation\DocumentationVersionPayload;
use App\Content\Documentation\HeadingPayload;
use App\Content\Documentation\ListPayload;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use Config\General;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

class CollectDocumentationVersionsFromPathTest extends TestCase
{
    /** @var CollectDocumentationVersionsFromPath */
    private $collectDocumentationVersionsFromPath;

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = ($this->collectDocumentationVersionsFromPath)(
            'Software/AnselCraft'
        );

        $softwareInfo = $payload->getSoftwareInfo();
        self::assertSame('Ansel', $softwareInfo->getTitle());
        self::assertSame('for Craft CMS', $softwareInfo->getSubTitle());
        self::assertFalse($softwareInfo->getForSale());
        self::assertTrue($softwareInfo->getHasChangelog());

        $versions = $payload->getVersions();
        self::assertCount(3, $versions);

        $slugs = [];

        foreach ($versions as $version) {
            $slugs[] = $version->getSlug();
        }

        self::assertSame(
            [
                'documentation',
                'documentation-v2',
                'documentation-a1',
            ],
            $slugs
        );

        self::assertNull($payload->getVersionBySlug('foo-bar'));

        $documentation = $payload->getVersionBySlug('documentation');

        self::assertNotNull($documentation);

        $this->validatePrimaryVersion($documentation);

        $v2 = $payload->getVersionBySlug('documentation-v2');

        self::assertNotNull($v2);

        $this->validateV2($v2);

        $a1 = $payload->getVersionBySlug('documentation-a1');

        self::assertNotNull($a1);

        $this->validateA1($a1);
    }

    private function validatePrimaryVersion(
        DocumentationVersionPayload $version
    ) : void {
        self::assertSame('Ansel 3.x for Craft (current)', $version->getTitle());
        self::assertSame('documentation', $version->getSlug());
        self::assertSame('primary', $version->getVersion());

        $pages = $version->getPages();
        self::assertCount(4, $pages);

        $page1 = $version->getPageBySlug('');
        self::assertNotNull($page1);
        self::assertSame('Getting Started', $page1->getTitle());
        self::assertSame('', $page1->getSlug());

        $page1Sections = $page1->getSections();
        self::assertCount(4, $page1Sections);

        $page1Section1 = $page1Sections[0];
        self::assertSame('System Requirements', $page1Section1->getTitle());
        $page1Section1Content = $page1Section1->getContent();

        /** @var HeadingPayload $page1Section1Content1HeadingPayload */
        $page1Section1Content1HeadingPayload = $page1Section1Content[0];
        self::assertSame(3, $page1Section1Content1HeadingPayload->getLevel());
        self::assertSame('General Requirements', $page1Section1Content1HeadingPayload->getContent());

        /** @var ListPayload $page1Section1Content4ListPayload */
        $page1Section1Content4ListPayload = $page1Section1Content[3];
        self::assertSame(
            [
                'Edge 41 or greater',
                'Internet Explorer 11 or greater',
                'Chrome (tested on version 65)',
                'Firefox (tested on version 59)',
                'Safari (Mac) (tested on version 11)',
            ],
            $page1Section1Content4ListPayload->getListItems()
        );

        $page4 = $version->getPageBySlug('templating');
        self::assertNotNull($page4);
        self::assertSame('Templating', $page4->getTitle());
        self::assertSame('templating', $page4->getSlug());

        $page4Sections = $page4->getSections();
        self::assertCount(5, $page4Sections);

        $page4Section1 = $page4Sections[0];
        self::assertSame('Ansel Image Service', $page4Section1->getTitle());

        $page4Section1Content = $page4Section1->getContent();
        self::assertCount(4, $page4Section1Content);

        /** @var CodeblockPayload $page4Section1Content2CodeBlock */
        $page4Section1Content2CodeBlock = $page4Section1Content[1];
        self::assertSame('Note', $page4Section1Content2CodeBlock->getHeading());
        self::assertSame('twig', $page4Section1Content2CodeBlock->getLang());
    }

    private function validateV2(DocumentationVersionPayload $version) : void
    {
        self::assertSame('Ansel 2.x for Craft (legacy)', $version->getTitle());
        self::assertSame('documentation-v2', $version->getSlug());
        self::assertSame('v2', $version->getVersion());

        $pages = $version->getPages();
        self::assertCount(1, $pages);

        self::assertNull($version->getPageBySlug('bar-foo'));
    }

    private function validateA1(DocumentationVersionPayload $version) : void
    {
        self::assertSame('Ansel 1.x for Craft (legacy)', $version->getTitle());
        self::assertSame('documentation-a1', $version->getSlug());
        self::assertSame('a1', $version->getVersion());

        $pages = $version->getPages();
        self::assertCount(4, $pages);

        self::assertNull($version->getPageBySlug('bar-foo'));
    }

    protected function setUp() : void
    {
        $generalConfig = $this->mockGeneralConfig();

        $extractSoftwareInfoFromPath = new ExtractSoftwareInfoFromPath(
            $generalConfig->pathToContentDirectory()
        );

        $collectDocumentationPageSectionFromPath = new CollectDocumentationPageSectionFromPath(
            $generalConfig
        );

        $collectDocumentationPagePayloadFromPath = new CollectDocumentationPagePayloadFromPath(
            $generalConfig,
            $collectDocumentationPageSectionFromPath
        );

        $collectDocumentationVersionPayloadFromPath = new CollectDocumentationVersionPayloadFromPath(
            $generalConfig,
            $collectDocumentationPagePayloadFromPath
        );

        $this->collectDocumentationVersionsFromPath = new CollectDocumentationVersionsFromPath(
            $generalConfig,
            $extractSoftwareInfoFromPath,
            $collectDocumentationVersionPayloadFromPath
        );
    }

    /**
     * @return General&MockObject
     */
    private function mockGeneralConfig()
    {
        /** @var General&MockObject $generalConfig */
        $generalConfig = $this->getMockBuilder(General::class)
            ->addMethods(['pathToContentDirectory'])
            ->getMock();

        $generalConfig->method('pathToContentDirectory')->willReturn(
            __DIR__ . '/TestContent'
        );

        return $generalConfig;
    }
}
