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

class GetAdminSearchUsersDisplayAction
{
    private ExtractUriSegments $extractUriSegments;
    private UserApi $userApi;
    private GetAdminResponder $responder;

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
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $query = (string) ($request->getQueryParams()['q'] ?? '');

        if ($query === '') {
            throw new HttpNotFoundException($request);
        }

        $limit = 50;

        $uriSegments = ($this->extractUriSegments)($request->getUri());

        $pageZeroIndex = $uriSegments->getPageNum() - 1;

        $offset = $pageZeroIndex * $limit;

        $users = $this->userApi->fetchUsersBySearch(
            '%' . $query . '%',
            $limit,
            $offset
        );

        $pagination = (new Pagination())
            ->withBase($uriSegments->getPathSansPagination())
            ->withCurrentPage($uriSegments->getPageNum())
            ->withPerPage($limit)
            ->withTotalResults($this->userApi->fetchTotalUsers())
            ->withQueryStringFromArray(['q' => $query]);

        return ($this->responder)(
            'Http/Admin/Users.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'User Search | Admin']
                ),
                'breadcrumbs' => [
                    [
                        'href' => '/admin/users',
                        'content' => 'Users Admin',
                    ],
                ],
                'activeTab' => 'users',
                'userModels' => $users,
                'pagination' => $pagination,
                'searchTerm' => $query,
            ]
        );
    }
}
