<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Console;

use Discuz\Console\Event\Configuring;
use Discuz\Foundation\SiteApp;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use ReflectionClass;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;

class Kernel extends SiteApp implements KernelContract
{
    /**
     * The output from the previous command.
     *
     * @var \Symfony\Component\Console\Output\BufferedOutput
     */
    protected $lastOutput;

    protected $disco;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->app->booted(function () {
            $this->defineConsoleSchedule();
        });
    }

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function defineConsoleSchedule()
    {
        $this->app->singleton(Schedule::class, function ($app) {
            return tap(new Schedule($this->scheduleTimezone()), function (Schedule $schedule) {
                $this->schedule($schedule->useCache($this->scheduleCache()));
            });
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

    protected function getName()
    {
        return <<<EOF
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
        $this->app->register(ConsoleServiceProvider::class);
    }

    /**
     * Handle an incoming console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        // TODO: Implement handle() method.
    }

    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        $this->siteBoot();

        $console = $this->getDisco();

        $this->app['events']->dispatch(new Configuring($console, $this->app));

        [$command, $input] = $this->parseCommand($command, $parameters);

        return $console->get($command)->run($input, $this->lastOutput = $outputBuffer ?: new BufferedOutput());
    }

    /**
     * Queue an Artisan console command by name.
     *
     * @param string $command
     * @param array $parameters
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
     * @param int $status
     * @return void
     */
    public function terminate($input, $status)
    {
        // TODO: Implement terminate() method.
    }

    public function getDisco(): ConsoleApplication
    {
        return $this->disco ?? $this->disco = new ConsoleApplication($this->getName(), Application::VERSION);
    }

    public function setDisco(ConsoleApplication $application)
    {
        $this->disco = $application;
    }


    /**
     * @param ConsoleApplication $console
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
        foreach ((new Finder)->in($paths)->files() as $command) {
            $command = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($command->getPathname(), realpath(app_path()).DIRECTORY_SEPARATOR)
            );
            if (is_subclass_of($command, Command::class) &&
                ! (new ReflectionClass($command))->isAbstract()) {
                $console->add($this->app->make($command));
            }
        }
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
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
        return ;
    }

    /**
     * Parse the incoming Artisan command and its input.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return array
     */
    protected function parseCommand($command, $parameters)
    {
        if (is_subclass_of($command, Command::class)) {
            $callingClass = true;

            $command = $this->app->make($command)->getName();
        }

        if (! isset($callingClass) && empty($parameters)) {
            $input = new StringInput($command);
        } else {
            array_unshift($parameters, $command);

            $input = new ArrayInput($parameters);
        }

        return [$command, $input ?? null];
    }
}
