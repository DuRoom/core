<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\unit\Install;

use DuRoom\Install\BaseUrl;
use DuRoom\Testing\unit\TestCase;
use Laminas\Diactoros\Uri;

class BaseUrlTest extends TestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function test_base_url_simulating_cli_installer($uri, $expected)
    {
        $this->assertEquals($expected, BaseUrl::fromString($uri));
    }

    /**
     * @dataProvider urlProvider
     */
    public function test_base_url_simulating_web_installer($uri, $expected)
    {
        $uri = new Uri($uri);

        $this->assertEquals($expected, BaseUrl::fromUri($uri));
    }

    /**
     * @dataProvider emailProvider
     */
    public function test_default_email_generation($uri, $expected)
    {
        $this->assertEquals(
            $expected,
            BaseUrl::fromString($uri)->toEmail('noreply')
        );
    }

    public function urlProvider()
    {
        return [
            ['',                                         ''],
            ['duroom.org',                               'http://duroom.org'],
            ['duroom.org/',                              'http://duroom.org'],
            ['http://duroom.org',                        'http://duroom.org'],
            ['http://duroom.org/',                       'http://duroom.org'],
            ['https://duroom.org',                       'https://duroom.org'],
            ['http://duroom.org/index.php',              'http://duroom.org'],
            ['http://duroom.org/index.php/',             'http://duroom.org'],
            ['http://duroom.org/duroom',                 'http://duroom.org/duroom'],
            ['http://duroom.org/duroom/index.php',       'http://duroom.org/duroom'],
            ['http://duroom.org/duroom/index.php/',      'http://duroom.org/duroom'],
            ['sub.duroom.org',                           'http://sub.duroom.org'],
            ['http://sub.duroom.org',                    'http://sub.duroom.org'],
            ['duroom.org:8000',                          'http://duroom.org:8000'],
            ['duroom.org:8000/',                         'http://duroom.org:8000'],
            ['http://duroom.org:8000',                   'http://duroom.org:8000'],
            ['http://duroom.org:8000/',                  'http://duroom.org:8000'],
            ['https://duroom.org:8000',                  'https://duroom.org:8000'],
            ['http://duroom.org:8000/index.php',         'http://duroom.org:8000'],
            ['http://duroom.org:8000/index.php/',        'http://duroom.org:8000'],
            ['http://duroom.org:8000/duroom',            'http://duroom.org:8000/duroom'],
            ['http://duroom.org:8000/duroom/index.php',  'http://duroom.org:8000/duroom'],
            ['http://duroom.org:8000/duroom/index.php/', 'http://duroom.org:8000/duroom'],
            ['sub.duroom.org:8000',                      'http://sub.duroom.org:8000'],
            ['http://sub.duroom.org:8000',               'http://sub.duroom.org:8000'],
        ];
    }

    public function emailProvider()
    {
        return [
            ['duroom.org',                               'noreply@duroom.org'],
            ['duroom.org/',                              'noreply@duroom.org'],
            ['http://duroom.org',                        'noreply@duroom.org'],
            ['http://duroom.org/',                       'noreply@duroom.org'],
            ['https://duroom.org',                       'noreply@duroom.org'],
            ['http://duroom.org/index.php',              'noreply@duroom.org'],
            ['http://duroom.org/index.php/',             'noreply@duroom.org'],
            ['http://duroom.org/duroom',                 'noreply@duroom.org'],
            ['http://duroom.org/duroom/index.php',       'noreply@duroom.org'],
            ['http://duroom.org/duroom/index.php/',      'noreply@duroom.org'],
            ['sub.duroom.org',                           'noreply@sub.duroom.org'],
            ['http://sub.duroom.org',                    'noreply@sub.duroom.org'],
            ['duroom.org:8000',                          'noreply@duroom.org'],
            ['duroom.org:8000/',                         'noreply@duroom.org'],
            ['http://duroom.org:8000',                   'noreply@duroom.org'],
            ['http://duroom.org:8000/',                  'noreply@duroom.org'],
            ['https://duroom.org:8000',                  'noreply@duroom.org'],
            ['http://duroom.org:8000/index.php',         'noreply@duroom.org'],
            ['http://duroom.org:8000/index.php/',        'noreply@duroom.org'],
            ['http://duroom.org:8000/duroom',            'noreply@duroom.org'],
            ['http://duroom.org:8000/duroom/index.php',  'noreply@duroom.org'],
            ['http://duroom.org:8000/duroom/index.php/', 'noreply@duroom.org'],
            ['sub.duroom.org:8000',                      'noreply@sub.duroom.org'],
            ['http://sub.duroom.org:8000',               'noreply@sub.duroom.org'],
        ];
    }
}