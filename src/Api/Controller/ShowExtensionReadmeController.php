<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use DuRoom\Api\Serializer\ExtensionReadmeSerializer;
use DuRoom\Extension\ExtensionManager;
use DuRoom\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowExtensionReadmeController extends AbstractShowController
{
    /**
     * @var ExtensionManager
     */
    protected $extensions;

    /**
     * {@inheritdoc}
     */
    public $serializer = ExtensionReadmeSerializer::class;

    public function __construct(ExtensionManager $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $extensionName = Arr::get($request->getQueryParams(), 'name');

        RequestUtil::getActor($request)->assertAdmin();

        return $this->extensions->getExtension($extensionName);
    }
}
