<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use DuRoom\Api\Serializer\UserSerializer;
use DuRoom\Http\RequestUtil;
use DuRoom\Http\UrlGenerator;
use DuRoom\Query\QueryCriteria;
use DuRoom\User\Filter\UserFilterer;
use DuRoom\User\Search\UserSearcher;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListUsersController extends AbstractListController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = UserSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['groups'];

    /**
     * {@inheritdoc}
     */
    public $sortFields = [
        'username',
        'commentCount',
        'discussionCount',
        'lastSeenAt',
        'joinedAt'
    ];

    /**
     * @var UserFilterer
     */
    protected $filterer;

    /**
     * @var UserSearcher
     */
    protected $searcher;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @param UserFilterer $filterer
     * @param UserSearcher $searcher
     * @param UrlGenerator $url
     */
    public function __construct(UserFilterer $filterer, UserSearcher $searcher, UrlGenerator $url)
    {
        $this->filterer = $filterer;
        $this->searcher = $searcher;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);

        $actor->assertCan('searchUsers');

        if (! $actor->hasPermission('user.viewLastSeenAt')) {
            // If a user cannot see everyone's last online date, we prevent them from sorting by it
            // Otherwise this sort field would defeat the privacy setting discloseOnline
            // We use remove instead of add so that extensions can still completely disable the sort using the extender
            $this->removeSortField('lastSeenAt');
        }

        $filters = $this->extractFilter($request);
        $sort = $this->extractSort($request);
        $sortIsDefault = $this->sortIsDefault($request);

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $include = $this->extractInclude($request);

        $criteria = new QueryCriteria($actor, $filters, $sort, $sortIsDefault);
        if (array_key_exists('q', $filters)) {
            $results = $this->searcher->search($criteria, $limit, $offset);
        } else {
            $results = $this->filterer->filter($criteria, $limit, $offset);
        }

        $document->addPaginationLinks(
            $this->url->to('api')->route('users.index'),
            $request->getQueryParams(),
            $offset,
            $limit,
            $results->areMoreResults() ? null : 0
        );

        $results = $results->getResults();

        $this->loadRelations($results, $include, $request);

        return $results;
    }
}
