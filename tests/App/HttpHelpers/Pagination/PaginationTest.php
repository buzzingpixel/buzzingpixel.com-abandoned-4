<?php

declare(strict_types=1);

namespace Tests\App\HttpHelpers\Pagination;

use App\HttpHelpers\Pagination\Pagination;
use LogicException;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    /** @var Pagination */
    private $pagination;

    protected function setUp() : void
    {
        $this->pagination = new Pagination();
    }

    public function testConstructorCanOnlyBeCalledOnce() : void
    {
        self::expectException(LogicException::class);

        self::expectExceptionMessage('Instance may only be instantiated once');

        $this->pagination->__construct();
    }

    public function testPad() : void
    {
        $paginationOne   = $this->pagination;
        $paginationTwo   = $this->pagination->withPad(14);
        $paginationThree = $paginationTwo->withPad(456);

        self::assertSame(2, $paginationOne->pad());
        self::assertSame(14, $paginationTwo->pad());
        self::assertSame(456, $paginationThree->pad());
    }

    public function testCurrentPage() : void
    {
        $paginationOne   = $this->pagination;
        $paginationTwo   = $this->pagination->withCurrentPage(45);
        $paginationThree = $paginationTwo->withCurrentPage(3453);

        self::assertSame(1, $paginationOne->currentPage());
        self::assertSame(45, $paginationTwo->currentPage());
        self::assertSame(3453, $paginationThree->currentPage());
    }

    public function testPerPage() : void
    {
        $paginationOne   = $this->pagination;
        $paginationTwo   = $this->pagination->withPerPage(123);
        $paginationThree = $paginationTwo->withPerPage(976);

        self::assertSame(12, $paginationOne->perPage());
        self::assertSame(123, $paginationTwo->perPage());
        self::assertSame(976, $paginationThree->perPage());
    }

    public function testTotalResults() : void
    {
        $paginationOne   = $this->pagination;
        $paginationTwo   = $this->pagination->withTotalResults(34);
        $paginationThree = $paginationTwo->withTotalResults(475);

        self::assertSame(1, $paginationOne->totalResults());
        self::assertSame(34, $paginationTwo->totalResults());
        self::assertSame(475, $paginationThree->totalResults());
    }

    public function testBase() : void
    {
        $paginationOne   = $this->pagination;
        $paginationTwo   = $this->pagination->withBase('test/base');
        $paginationThree = $paginationTwo->withBase('another');

        self::assertSame('', $paginationOne->base());
        self::assertSame('/test/base', $paginationTwo->base());
        self::assertSame('/another', $paginationThree->base());
    }

    public function testQueryString() : void
    {
        $paginationOne   = $this->pagination;
        $paginationTwo   = $this->pagination->withQueryString('?myQuery=myThing');
        $paginationThree = $paginationTwo->withQueryString('?another=query&anotherThing');
        $paginationFour  = $paginationThree->withQueryStringFromArray([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        self::assertSame('', $paginationOne->queryString());
        self::assertSame('?myQuery=myThing', $paginationTwo->queryString());
        self::assertSame('?another=query&anotherThing', $paginationThree->queryString());
        self::assertSame('?foo=bar&bar=baz', $paginationFour->queryString());
    }

    public function testTotalPages() : void
    {
        $paginationOne   = $this->pagination;
        $paginationTwo   = $this->pagination->withTotalResults(131);
        $paginationThree = $this->pagination->withTotalResults(132);
        $paginationFour  = $this->pagination->withTotalResults(133);
        $paginationFive  = $this->pagination->withTotalResults(134);

        self::assertSame(1, $paginationOne->totalPages());
        self::assertSame(11, $paginationTwo->totalPages());
        self::assertSame(11, $paginationThree->totalPages());
        self::assertSame(12, $paginationFour->totalPages());
        self::assertSame(12, $paginationFive->totalPages());
    }

    public function testPrevPageAndPrevPageLink() : void
    {
        $paginationOne           = $this->pagination;
        $paginationOneWithBase   = $paginationOne->withBase('/foo/bar');
        $paginationTwo           = $paginationOne->withCurrentPage(2);
        $paginationTwoWithBase   = $paginationTwo->withBase('/bar/baz');
        $paginationThree         = $paginationTwo->withCurrentPage(987);
        $paginationThreeWithBase = $paginationThree->withBase('/baz/foo');

        self::assertNull($paginationOne->prevPage());
        self::assertNull($paginationOne->prevPageLink());
        self::assertNull($paginationOneWithBase->prevPageLink());

        self::assertSame(1, $paginationTwo->prevPage());
        self::assertSame('', $paginationTwo->prevPageLink());
        self::assertSame('/bar/baz', $paginationTwoWithBase->prevPageLink());

        self::assertSame(986, $paginationThree->prevPage());
        self::assertSame('/page/986', $paginationThree->prevPageLink());
        self::assertSame('/baz/foo/page/986', $paginationThreeWithBase->prevPageLink());
    }

    public function testNextPageAndNextPageLink() : void
    {
        $paginationOne           = $this->pagination;
        $paginationOneWithBase   = $paginationOne->withBase('/foo/bar');
        $paginationTwo           = $paginationOne->withCurrentPage(2)->withTotalResults(40);
        $paginationTwoWithBase   = $paginationTwo->withBase('/bar/baz');
        $paginationThree         = $paginationTwo->withCurrentPage(3);
        $paginationThreeWithBase = $paginationThree->withBase('/baz/foo');
        $paginationFour          = $paginationThree->withCurrentPage(4);
        $paginationFourWithBase  = $paginationFour->withBase('/foo/baz');

        self::assertNull($paginationOne->nextPage());
        self::assertNull($paginationOne->nextPageLink());
        self::assertNull($paginationOneWithBase->nextPageLink());

        self::assertSame(3, $paginationTwo->nextPage());
        self::assertSame('/page/3', $paginationTwo->nextPageLink());
        self::assertSame('/bar/baz/page/3', $paginationTwoWithBase->nextPageLink());

        self::assertSame(4, $paginationThree->nextPage());
        self::assertSame('/page/4', $paginationThree->nextPageLink());
        self::assertSame('/baz/foo/page/4', $paginationThreeWithBase->nextPageLink());

        self::assertNull($paginationFour->nextPage());
        self::assertNull($paginationFour->nextPageLink());
        self::assertNull($paginationFourWithBase->nextPageLink());
    }

    public function testFirstPageLink() : void
    {
        $paginationOne           = $this->pagination;
        $paginationOneWithBase   = $paginationOne->withBase('/foo/bar');
        $paginationTwo           = $paginationOne->withCurrentPage(6);
        $paginationTwoWithBase   = $paginationTwo->withBase('/bar/baz');
        $paginationThree         = $paginationTwo->withCurrentPage(987);
        $paginationThreeWithBase = $paginationThree->withBase('/baz/foo');

        self::assertNull($paginationOne->firstPageLink());
        self::assertNull($paginationOneWithBase->firstPageLink());

        self::assertSame('/', $paginationTwo->firstPageLink());
        self::assertSame('/bar/baz', $paginationTwoWithBase->firstPageLink());

        self::assertSame('/', $paginationThree->firstPageLink());
        self::assertSame('/baz/foo', $paginationThreeWithBase->firstPageLink());
    }

    public function testLastPageLink() : void
    {
        $paginationOne           = $this->pagination;
        $paginationOneWithBase   = $paginationOne->withBase('/foo/bar');
        $paginationTwo           = $paginationOne->withTotalResults(100)->withCurrentPage(2);
        $paginationTwoWithBase   = $paginationTwo->withBase('/bar/baz');
        $paginationThree         = $paginationTwo->withCurrentPage(9);
        $paginationThreeWithBase = $paginationThree->withBase('/baz/foo');

        self::assertNull($paginationOne->lastPageLink());
        self::assertNull($paginationOneWithBase->lastPageLink());

        self::assertSame('/page/9', $paginationTwo->lastPageLink());
        self::assertSame('/bar/baz/page/9', $paginationTwoWithBase->lastPageLink());

        self::assertNull($paginationThree->lastPageLink());
        self::assertNull($paginationThreeWithBase->lastPageLink());
    }

    public function testPagesArrayWithPageOneAndNoAdditionalPages() : void
    {
        $pagination = $this->pagination;

        self::assertIsArray($pagination->pagesArray());
        self::assertEmpty($pagination->pagesArray());
    }

    public function testPagesArrayWithPageOneAndOneAdditionalPage() : void
    {
        $pagination  = $this->pagination->withTotalResults(13);
        $pagination2 = $pagination->withBase('foo/bar');

        self::assertSame(
            [
                [
                    'label' => 1,
                    'target' => '',
                    'isActive' => true,
                ],
                [
                    'label' => 2,
                    'target' => '/page/2',
                    'isActive' => false,
                ],
            ],
            $pagination->pagesArray()
        );

        self::assertSame(
            [
                [
                    'label' => 1,
                    'target' => '/foo/bar',
                    'isActive' => true,
                ],
                [
                    'label' => 2,
                    'target' => '/foo/bar/page/2',
                    'isActive' => false,
                ],
            ],
            $pagination2->pagesArray()
        );
    }

    public function testPageArrayWithFifteenAdditionalPages() : void
    {
        $pagination  = $this->pagination->withTotalResults(179)->withCurrentPage(15);
        $pagination2 = $pagination->withCurrentPage(16);

        $paginationArray = [
            [
                'label' => 11,
                'target' => '/page/11',
                'isActive' => false,
            ],
            [
                'label' => 12,
                'target' => '/page/12',
                'isActive' => false,
            ],
            [
                'label' => 13,
                'target' => '/page/13',
                'isActive' => false,
            ],
            [
                'label' => 14,
                'target' => '/page/14',
                'isActive' => false,
            ],
            [
                'label' => 15,
                'target' => '/page/15',
                'isActive' => true,
            ],
        ];

        $paginationArray2 = $paginationArray;

        $paginationArray2[4]['isActive'] = false;

        self::assertSame($paginationArray, $pagination->pagesArray());
        self::assertSame($paginationArray, $pagination->pagesArray());

        self::assertSame($paginationArray2, $pagination2->pagesArray());
    }
}
