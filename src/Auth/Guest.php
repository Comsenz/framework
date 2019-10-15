<?php


namespace Discuz\Auth;


use App\Models\Group;
use App\Models\User;

class Guest extends User
{
    public $id = 0;

    /**
     * belongsToMany 游客获取多对多关系时候会自动变成当前类名，group_guest，游客处理多对多关系时手动处理，以免报表不存在错误。
     * @return mixed
     */
    public function getGroupsAttribute()
    {
        if (! isset($this->attributes['groups'])) {
            $this->attributes['groups'] = $this->relations['groups'] = Group::where('id', Group::GUEST_ID)->get();
        }

        return $this->attributes['groups'];
    }

    public function isGuest() {
        return true;
    }
}
