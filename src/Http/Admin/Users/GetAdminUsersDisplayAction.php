<?php

declare(strict_types=1);

namespace App\Http\Admin\Users;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use App\HttpHelpers\Pagination\Pagination;
use App\HttpHelpers\Segments\ExtractUriSegments;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function count;

class GetAdminUsersDisplayAction
{
    /** @var ExtractUriSegments */
    private $extractUriSegments;
    /** @var UserApi */
    private $userApi;
    /** @var GetAdminResponder */
    private $responder;

    public function __construct(
        ExtractUriSegments $extractUriSegments,
        UserApi $userApi,
        GetAdminResponder $responder
    ) {
        $this->extractUriSegments = $extractUriSegments;
        $this->userApi            = $userApi;
        $this->responder          = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $limit = 50;

        $uriSegments = ($this->extractUriSegments)($request->getUri());

        $pageZeroIndex = $uriSegments->getPageNum() - 1;

        $offset = $pageZeroIndex * $limit;

        $userModels = $this->userApi->fetchUsersByLimitOffset(
            $limit,
            $offset
        );

        if (count($userModels) < 1) {
            throw new HttpNotFoundException($request);
        }

        $pagination = (new Pagination())->withBase($uriSegments->getPathSansPagination())
            ->withCurrentPage($uriSegments->getPageNum())
            ->withPerPage($limit)
            ->withTotalResults($this->userApi->fetchTotalUsers());

        return ($this->responder)(
            'Admin/Users.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Users | Admin']
                ),
                'activeTab' => 'users',
                'userModels' => $userModels,
                'pagination' => $pagination,
            ]
        );
    }
}
