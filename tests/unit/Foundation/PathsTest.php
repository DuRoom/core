<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\unit\Foundation;

use DuRoom\Foundation\Paths;
use DuRoom\Testing\unit\TestCase;
use InvalidArgumentException;

class PathsTest extends TestCase
{
    /** @test */
    public function it_complains_when_paths_are_missing()
    {
        $this->expectException(InvalidArgumentException::class);

        new Paths([
            'base' => '/var/www/duroom',
        ]);
    }

    /** @test */
    public function it_makes_paths_available_as_properties()
    {
        $paths = new Paths([
            'base' => '/var/www/duroom',
            'public' => '/var/www/duroom/public',
            'storage' => '/var/www/duroom/storage',
        ]);

        $this->assertEquals('/var/www/duroom', $paths->base);
        $this->assertEquals('/var/www/duroom/public', $paths->public);
        $this->assertEquals('/var/www/duroom/storage', $paths->storage);
    }

    /** @test */
    public function it_derives_the_vendor_dir_from_the_base_path()
    {
        $paths = new Paths([
            'base' => '/var/www/duroom',
            'public' => '/var/www/duroom/public',
            'storage' => '/var/www/duroom/storage',
        ]);

        $this->assertEquals('/var/www/duroom/vendor', $paths->vendor);
    }

    /** @test */
    public function it_allows_setting_a_custom_vendor_dir()
    {
        $paths = new Paths([
            'base' => '/var/www/duroom',
            'public' => '/var/www/duroom/public',
            'storage' => '/var/www/duroom/storage',
            'vendor' => '/share/composer-vendor',
        ]);

        $this->assertEquals('/share/composer-vendor', $paths->vendor);
    }

    /** @test */
    public function it_strips_trailing_forward_slashes_from_paths()
    {
        $paths = new Paths([
            'base' => '/var/www/duroom/',
            'public' => '/var/www/duroom/public/',
            'storage' => '/var/www/duroom/storage/',
        ]);

        $this->assertEquals('/var/www/duroom', $paths->base);
        $this->assertEquals('/var/www/duroom/public', $paths->public);
        $this->assertEquals('/var/www/duroom/storage', $paths->storage);
    }

    /** @test */
    public function it_strips_trailing_backslashes_from_paths()
    {
        $paths = new Paths([
            'base' => 'C:\\duroom\\',
            'public' => 'C:\\duroom\\public\\',
            'storage' => 'C:\\duroom\\storage\\',
        ]);

        $this->assertEquals('C:\\duroom', $paths->base);
        $this->assertEquals('C:\\duroom\\public', $paths->public);
        $this->assertEquals('C:\\duroom\\storage', $paths->storage);
    }
}