<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use DuRoom\Database\Migration;

return Migration::renameColumns('posts', [
    'time' => 'created_at',
    'edit_time' => 'edited_at',
    'hide_time' => 'hidden_at',
    'edit_user_id' => 'edited_user_id',
    'hide_user_id' => 'hidden_user_id'
]);
