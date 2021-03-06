<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use DuRoom\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable(
    'users_groups',
    function (Blueprint $table) {
        $table->integer('user_id')->unsigned();
        $table->integer('group_id')->unsigned();
        $table->primary(['user_id', 'group_id']);
    }
);
