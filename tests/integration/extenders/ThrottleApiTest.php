<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\integration\extenders;

use DuRoom\Extend;
use DuRoom\Testing\integration\RetrievesAuthorizedUsers;
use DuRoom\Testing\integration\TestCase;

class ThrottleApiTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
            ]
        ]);
    }

    /**
     * @test
     */
    public function list_discussions_not_restricted_by_default()
    {
        $response = $this->send($this->request('GET', '/api/discussions', ['authenticatedAs' => 2]));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function list_discussions_can_be_restricted()
    {
        $this->extend((new Extend\ThrottleApi)->set('blockListDiscussions', function ($request) {
            if ($request->getAttribute('routeName') === 'discussions.index') {
                return true;
            }
        }));

        $response = $this->send($this->request('GET', '/api/discussions', ['authenticatedAs' => 2]));

        $this->assertEquals(429, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function false_overrides_true_for_evaluating_throttlers()
    {
        $this->extend(
            (new Extend\ThrottleApi)->set('blockListDiscussions', function ($request) {
                if ($request->getAttribute('routeName') === 'discussions.index') {
                    return true;
                }
            }),
            (new Extend\ThrottleApi)->set('blockListDiscussionsOverride', function ($request) {
                if ($request->getAttribute('routeName') === 'discussions.index') {
                    return false;
                }
            })
        );

        $response = $this->send($this->request('GET', '/api/discussions', ['authenticatedAs' => 2]));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function throttling_applies_to_api_client()
    {
        $this->extend((new Extend\ThrottleApi)->set('blockRegistration', function ($request) {
            if ($request->getAttribute('routeName') === 'users.create') {
                return true;
            }
        }));

        $response = $this->send($this->request('POST', '/register')->withAttribute('bypassCsrfToken', true));

        $this->assertEquals(429, $response->getStatusCode());
    }
}