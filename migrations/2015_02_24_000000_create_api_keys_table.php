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
    'api_keys',
    function (Blueprint $table) {
        $table->string('id', 100)->primary();
    }
);
