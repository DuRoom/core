<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\integration\middleware;

use DuRoom\Testing\integration\TestCase;

class ContentTypeOptionsTest extends TestCase
{
    /**
     * @test
     */
    public function has_content_type_options_header()
    {
        $response = $this->send(
            $this->request('GET', '/')
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('X-Content-Type-Options', $response->getHeaders());
        $this->assertEquals('nosniff', $response->getHeader('X-Content-Type-Options')[0]);
    }
}