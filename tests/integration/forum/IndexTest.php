<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\integration\forum;

use DuRoom\Extend;
use DuRoom\Testing\integration\RetrievesAuthorizedUsers;
use DuRoom\Testing\integration\TestCase;

class IndexTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->extend(
            (new Extend\Csrf)->exemptRoute('login')
        );

        $this->prepareDatabase([
            'users' => [
                $this->normalUser()
            ]
        ]);
    }

    /**
     * @test
     */
    public function guest_not_serialized_by_current_user_serializer()
    {
        $response = $this->send(
            $this->request('GET', '/')
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringNotContainsString('preferences', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    public function user_serialized_by_current_user_serializer()
    {
        $login = $this->send(
            $this->request('POST', '/login', [
                'json' => [
                    'identification' => 'normal',
                    'password' => 'too-obscure'
                ]
            ])
        );

        $response = $this->send(
            $this->request('GET', '/', [
                'cookiesFrom' => $login
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('preferences', $response->getBody()->getContents());
    }
}