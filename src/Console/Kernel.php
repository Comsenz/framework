<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Console;

use Discuz\Console\Event\Configuring;
use Discuz\Database\MigrationServiceProvider;
use Discuz\Foundation\Application;
use Discuz\Foundation\SiteApp;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

class Kernel extends SiteApp implements KernelContract
{
    protected $disco;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->app->booted(function () {
            $this->defineConsoleSchedule();
        });
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function listen()
    {
        $this->siteBoot();

        $console = $this->getDisco();

        $this->app['events']->dispatch(new Configuring($console, $this->app));

        $this->load($console);

        exit($console->run());
    }

    /**
     * Handle an incoming console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface        $input
     * @param null|\Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function handle($input, $output = null)
    {
        // TODO: Implement handle() method.
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param string                                                 $command
     * @param null|\Symfony\Component\Console\Output\OutputInterface $outputBuffer
     *
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        // TODO: Implement call() method.
    }

    /**
     * Queue an Artisan console command by name.
     *
     * @param string $command
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function queue($command, array $parameters = [])
    {
        // TODO: Implement queue() method.
    }

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        // TODO: Implement output() method.
    }

    /**
     * Terminate the application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param int                                             $status
     */
    public function terminate($input, $status)
    {
        // TODO: Implement terminate() method.
    }

    public function getDisco(): ConsoleApplication
    {
        return $this->disco ?? $this->disco = new ConsoleApplication($this->getName(), Application::VERSION);
    }

    /**
     * Define the application's command schedule.
     */
    protected function defineConsoleSchedule()
    {
        $this->app->singleton(Schedule::class, function ($app) {
            return tap(new Schedule($this->scheduleTimezone()), function (Schedule $schedule) {
                $this->schedule($schedule->useCache($this->scheduleCache()));
            });
        });
    }

    protected function getName()
    {
        return <<<'EOF'
 _____   _                           _____   _                 
(____ \ (_)                         (____ \ (_)                
 _   \ \ _  ___  ____ _   _ _____    _   \ \ _  ___  ____ ___  
| |   | | |/___)/ ___) | | (___  )  | |   | | |/___)/ ___) _ \ 
| |__/ /| |___ ( (___| |_| |/ __/   | |__/ /| |___ ( (__| |_| |
|_____/ |_(___/ \____)\____(_____)  |_____/ |_(___/ \____)___/ 
EOF;
    }

    protected function registerServiceProvider()
    {
        $this->app->register(MigrationServiceProvider::class);
        $this->app->register(ConsoleServiceProvider::class);
    }

    /**
     * @throws \ReflectionException
     */
    protected function load(ConsoleApplication $console)
    {
        $paths = app_path('Console/Commands');
        $paths = array_unique(Arr::wrap($paths));
        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });
        if (empty($paths)) {
            return;
        }
        $namespace = $this->app->getNamespace();
        foreach ((new Finder())->in($paths)->files() as $command) {
            $command = $namespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($command->getPathname(), realpath(app_path()) . \DIRECTORY_SEPARATOR)
            );
            if (is_subclass_of($command, Command::class) &&
                !(new ReflectionClass($command))->isAbstract()) {
                $console->add($this->app->make($command));
            }
        }
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return null|\DateTimeZone|string
     */
    protected function scheduleTimezone()
    {
        return $this->app->config('timezone');
    }

    /**
     * Get the name of the cache store that should manage scheduling mutexes.
     *
     * @return string
     */
    protected function scheduleCache()
    {
        return Env::get('SCHEDULE_CACHE_DRIVER');
    }
}
