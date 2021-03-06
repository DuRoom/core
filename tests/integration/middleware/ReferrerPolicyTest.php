<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\integration\middleware;

use DuRoom\Testing\integration\TestCase;

class ReferrerPolicyTest extends TestCase
{
    /**
     * @test
     */
    public function has_referer_header()
    {
        $response = $this->send(
            $this->request('GET', '/')
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('Referrer-Policy', $response->getHeaders());
    }

    /**
     * @test
     */
    public function has_default_referer_policy()
    {
        $response = $this->send(
            $this->request('GET', '/')
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('same-origin', $response->getHeader('Referrer-Policy')[0]);
    }
}