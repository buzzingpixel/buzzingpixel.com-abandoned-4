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

class GetSearchUsersDisplayAction
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

        $query = (string) ($request->getQueryParams()['q'] ?? '');

        if ($query === null || $query === '') {
            throw new HttpNotFoundException($request);
        }

        $userModels = $this->userApi->fetchUsersBySearch(
            '%' . $query . '%',
            $limit,
            $offset
        );

        $pagination = (new Pagination())->withBase($uriSegments->getPathSansPagination())
            ->withCurrentPage($uriSegments->getPageNum())
            ->withPerPage($limit)
            ->withTotalResults($this->userApi->fetchTotalUsers());

        return ($this->responder)(
            'Admin/Users.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'User Search | Admin']
                ),
                'activeTab' => 'users',
                'userModels' => $userModels,
                'pagination' => $pagination,
                'searchTerm' => $query,
            ]
        );
    }
}
