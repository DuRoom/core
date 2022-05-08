<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extend;

use DuRoom\Extension\Extension;
use DuRoom\Post\Post as PostModel;
use Illuminate\Contracts\Container\Container;

class Post implements ExtenderInterface
{
    private $postTypes = [];

    /**
     * Register a new post type. This is generally done for custom 'event posts',
     * such as those that appear when a discussion is renamed.
     *
     * @param string $postType: The ::class attribute of the custom Post type that is being added.
     * @return self
     */
    public function type(string $postType): self
    {
        $this->postTypes[] = $postType;

        return $this;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        foreach ($this->postTypes as $postType) {
            PostModel::setModel($postType::$type, $postType);
        }
    }
}
