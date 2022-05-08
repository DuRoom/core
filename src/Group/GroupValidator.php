<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group;

use DuRoom\Foundation\AbstractValidator;

class GroupValidator extends AbstractValidator
{
    protected $rules = [
        'name_singular' => ['required'],
        'name_plural' => ['required']
    ];
}
