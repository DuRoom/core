<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use DuRoom\Api\Serializer\GroupSerializer;
use DuRoom\Group\Filter\GroupFilterer;
use DuRoom\Http\RequestUtil;
use DuRoom\Http\UrlGenerator;
use DuRoom\Query\QueryCriteria;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListGroupsController extends AbstractListController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = GroupSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $sortFields = ['nameSingular', 'namePlural', 'isHidden'];

    /**
     * {@inheritdoc}
     *
     * @var int
     */
    public $limit = -1;

    /**
     * @var GroupFilterer
     */
    protected $filterer;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @param GroupFilterer $filterer
     * @param UrlGenerator $url
     */
    public function __construct(GroupFilterer $filterer, UrlGenerator $url)
    {
        $this->filterer = $filterer;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);

        $filters = $this->extractFilter($request);
        $sort = $this->extractSort($request);
        $sortIsDefault = $this->sortIsDefault($request);

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);

        $criteria = new QueryCriteria($actor, $filters, $sort, $sortIsDefault);

        $queryResults = $this->filterer->filter($criteria, $limit, $offset);

        $document->addPaginationLinks(
            $this->url->to('api')->route('groups.index'),
            $request->getQueryParams(),
            $offset,
            $limit,
            $queryResults->areMoreResults() ? null : 0
        );

        $results = $queryResults->getResults();

        $this->loadRelations($results, [], $request);

        return $results;
    }
}
