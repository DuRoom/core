<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\unit\Foundation;

use DuRoom\Extension\ExtensionManager;
use DuRoom\Testing\unit\TestCase;

class ExtensionDependencyResolutionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->tags = new FakeExtension('duroom-tags', []);
        $this->categories = new FakeExtension('duroom-categories', ['duroom-tags', 'duroom-tag-backgrounds']);
        $this->tagBackgrounds = new FakeExtension('duroom-tag-backgrounds', ['duroom-tags']);
        $this->something = new FakeExtension('duroom-something', ['duroom-categories', 'duroom-help']);
        $this->help = new FakeExtension('duroom-help', []);
        $this->missing = new FakeExtension('duroom-missing', ['this-does-not-exist', 'duroom-tags', 'also-not-exists']);
        $this->circular1 = new FakeExtension('circular1', ['circular2']);
        $this->circular2 = new FakeExtension('circular2', ['circular1']);
        $this->optionalDependencyCategories = new FakeExtension('duroom-categories', ['duroom-tags'], ['duroom-tag-backgrounds']);
    }

    /** @test */
    public function works_with_empty_set()
    {
        $expected = [
            'valid' => [],
            'missingDependencies' => [],
            'circularDependencies' => [],
        ];

        $this->assertEquals($expected, ExtensionManager::resolveExtensionOrder([]));
    }

    /** @test */
    public function works_with_proper_data()
    {
        $exts = [$this->tags, $this->categories, $this->tagBackgrounds, $this->something, $this->help];

        $expected = [
            'valid' => [$this->tags, $this->tagBackgrounds, $this->help, $this->categories, $this->something],
            'missingDependencies' => [],
            'circularDependencies' => [],
        ];

        $this->assertEquals($expected, ExtensionManager::resolveExtensionOrder($exts));
    }

    /** @test */
    public function works_with_missing_dependencies()
    {
        $exts = [$this->tags, $this->categories, $this->tagBackgrounds, $this->something, $this->help, $this->missing];

        $expected = [
            'valid' => [$this->tags, $this->tagBackgrounds, $this->help, $this->categories, $this->something],
            'missingDependencies' => ['duroom-missing' => ['this-does-not-exist', 'also-not-exists']],
            'circularDependencies' => [],
        ];

        $this->assertEquals($expected, ExtensionManager::resolveExtensionOrder($exts));
    }

    /** @test */
    public function works_with_circular_dependencies()
    {
        $exts = [$this->tags, $this->categories, $this->tagBackgrounds, $this->something, $this->help, $this->circular1, $this->circular2];

        $expected = [
            'valid' => [$this->tags, $this->tagBackgrounds, $this->help, $this->categories, $this->something],
            'missingDependencies' => [],
            'circularDependencies' => ['circular2', 'circular1'],
        ];

        $this->assertEquals($expected, ExtensionManager::resolveExtensionOrder($exts));
    }

    /** @test */
    public function works_with_optional_dependencies()
    {
        $exts = [$this->tags, $this->optionalDependencyCategories, $this->tagBackgrounds, $this->something, $this->help];

        $expected = [
            'valid' => [$this->tags, $this->tagBackgrounds, $this->help, $this->optionalDependencyCategories, $this->something],
            'missingDependencies' => [],
            'circularDependencies' => [],
        ];

        $this->assertEquals($expected, ExtensionManager::resolveExtensionOrder($exts));
    }

    /** @test */
    public function works_with_optional_dependencies_if_optional_dependency_missing()
    {
        $exts = [$this->tags, $this->optionalDependencyCategories, $this->something, $this->help];

        $expected = [
            'valid' => [$this->tags, $this->help, $this->optionalDependencyCategories, $this->something],
            'missingDependencies' => [],
            'circularDependencies' => [],
        ];

        $this->assertEquals($expected, ExtensionManager::resolveExtensionOrder($exts));
    }
}

class FakeExtension
{
    protected $id;
    protected $extensionDependencies;
    protected $optionalDependencies;

    public function __construct($id, $extensionDependencies, $optionalDependencies = [])
    {
        $this->id = $id;
        $this->extensionDependencies = $extensionDependencies;
        $this->optionalDependencies = $optionalDependencies;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getExtensionDependencyIds()
    {
        return $this->extensionDependencies;
    }

    public function getOptionalDependencyIds()
    {
        return $this->optionalDependencies;
    }
}