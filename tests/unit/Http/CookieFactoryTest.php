<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\unit\Http;

use Carbon\Carbon;
use DuRoom\Foundation\Config;
use DuRoom\Http\CookieFactory;
use DuRoom\Testing\unit\TestCase;

class CookieFactoryTest extends TestCase
{
    protected function factory(array $config = null): CookieFactory
    {
        $config = new Config(array_merge([
            'url' => 'http://duroom.test'
        ], $config ?? []));

        return new CookieFactory($config);
    }

    /** @test */
    public function can_create_cookies()
    {
        $cookie = $this->factory()->make('test', 'australia');

        $this->assertEquals('duroom_test', $cookie->getName());
        $this->assertEquals('australia', $cookie->getValue());
        $this->assertEquals(0, $cookie->getExpires());
        $this->assertFalse($cookie->getSecure());
        $this->assertEquals('/', $cookie->getPath());
    }

    /** @test */
    public function can_override_cookie_settings_from_config()
    {
        $cookie = $this->factory([
            'cookie' => [
                'name' => 'australia',
                'secure' => true,
                'domain' => 'duroom.com',
                'samesite' => 'none'
            ]
        ])->make('test', 'australia');

        $this->assertEquals('australia_test', $cookie->getName());
        $this->assertTrue($cookie->getSecure());
        $this->assertEquals('duroom.com', $cookie->getDomain());
        $this->assertEquals('SameSite=None', $cookie->getSameSite()->asString());
    }

    /** @test */
    public function can_expire_cookies()
    {
        $cookie = $this->factory()->expire('test');

        $this->assertLessThan(Carbon::now()->timestamp, $cookie->getExpires());
    }
}