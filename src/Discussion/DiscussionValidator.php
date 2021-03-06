<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion;

use DuRoom\Foundation\AbstractValidator;

class DiscussionValidator extends AbstractValidator
{
    protected $rules = [
        'title' => [
            'required',
            'min:3',
            'max:80'
        ]
    ];
}
