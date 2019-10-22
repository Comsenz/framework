<?php


namespace Discuz\Auth;


use App\Models\User;
use Discuz\Foundation\AbstractPolicy;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends AbstractPolicy
{

    protected $model = User::class;


    /**
     * @param User $actor
     * @param Model $model
     * @param string $ability
     * @return bool
     */
    public function canPermission(User $actor, Model $model, $ability): bool
    {
        if ($actor->hasPermission('user.'.$ability)) {
            return true;
        }
    }
}
