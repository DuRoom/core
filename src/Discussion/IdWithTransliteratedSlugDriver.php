<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion;

use DuRoom\Database\AbstractModel;
use DuRoom\Http\SlugDriverInterface;
use DuRoom\User\User;

class IdWithTransliteratedSlugDriver implements SlugDriverInterface
{
    /**
     * @var DiscussionRepository
     */
    protected $discussions;

    public function __construct(DiscussionRepository $discussions)
    {
        $this->discussions = $discussions;
    }

    public function toSlug(AbstractModel $instance): string
    {
        return $instance->id.(trim($instance->slug) ? '-'.$instance->slug : '');
    }

    public function fromSlug(string $slug, User $actor): AbstractModel
    {
        if (strpos($slug, '-')) {
            $slug_array = explode('-', $slug);
            $slug = $slug_array[0];
        }

        return $this->discussions->findOrFail($slug, $actor);
    }
}
