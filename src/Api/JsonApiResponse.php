<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api;

use Laminas\Diactoros\Response\JsonResponse;
use Tobscure\JsonApi\Document;

class JsonApiResponse extends JsonResponse
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Document $document, $status = 200, array $headers = [], $encodingOptions = 15)
    {
        $headers['content-type'] = 'application/vnd.api+json';

        // The call to jsonSerialize prevents rare issues with json_encode() failing with a
        // syntax error even though Document implements the JsonSerializable interface.
        // See https://github.com/duroom/core/issues/685
        parent::__construct($document->jsonSerialize(), $status, $headers, $encodingOptions);
    }
}
