<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Auth;

use App\Models\Group;
use App\Models\User;

class Guest extends User
{
    /**
     * Override the ID of this user, as a guest does not have an ID.
     *
     * @var int
     */
    public $id = 0;

    /**
     * Get the guest's group, containing only the 'guests' group model.
     *
     * @return Group
     */
    public function getGroupsAttribute()
    {
        if (!isset($this->attributes['groups'])) {
            $this->attributes['groups'] = $this->relations['groups'] = Group::find(Group::GUEST_ID);
        }

        return $this->attributes['groups'];
    }

    /**
     * {@inheritdoc}
     */
    public function isGuest()
    {
        return true;
    }
}
