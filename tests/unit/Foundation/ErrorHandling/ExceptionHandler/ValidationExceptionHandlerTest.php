<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\unit\Foundation\ErrorHandling\ExceptionHandler;

use DuRoom\Foundation\ErrorHandling\ExceptionHandler\ValidationExceptionHandler;
use DuRoom\Foundation\ValidationException;
use DuRoom\Testing\unit\TestCase;

class ValidationExceptionHandlerTest extends TestCase
{
    private $handler;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->handler = new ValidationExceptionHandler;
    }

    public function test_managing_exceptions()
    {
        $error = $this->handler->handle(new ValidationException(
            ['foo' => 'Attribute error'],
            ['bar' => 'Relationship error']
        ));

        $this->assertEquals(422, $error->getStatusCode());
        $this->assertEquals('validation_error', $error->getType());
        $this->assertEquals([
            [
                'detail' => 'Attribute error',
                'source' => ['pointer' => '/data/attributes/foo']
            ],
            [
                'detail' => 'Relationship error',
                'source' => ['pointer' => '/data/relationships/bar']
            ]
        ], $error->getDetails());
    }
}