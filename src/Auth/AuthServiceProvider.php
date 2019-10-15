<?php


namespace Discuz\Auth;


use App\Models\User;
use Discuz\Api\Events\GetPermission;
use Discuz\Auth\UserPolicy;
use Discuz\Auth\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Carbon\Laravel\ServiceProvider;
use RuntimeException;

class AuthServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(GateContract::class, function ($app) {
            return new Gate($app, function () {
                throw new RuntimeException('You must set the gate user with forUser()');
            });
        });
    }

    public function boot() {

        $gate = $this->app->make(GateContract::class);
        $events = $this->app->make('events');

        $gate->before(function (User $actor, $ability, $model = null) use ($events) {
            $allowed = $events->until(
                new GetPermission($actor, $ability, $model)
            );

            if (! is_null($allowed)) {
                return $allowed;
            }

            if ($actor->isAdmin() || (! $model && $actor->hasPermission($ability))) {
                return true;
            }

            return false;
        });
        User::setGate($gate);



        $events->subscribe(UserPolicy::class);
    }
}
