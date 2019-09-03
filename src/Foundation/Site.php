<?php


namespace Discuz\Foundation;


use Barryvdh\Debugbar\ServiceProvider;
use Discuz\Web\WebServiceProvider;
use Exception;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Config\Repository as ConfigRepository;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class Site implements SiteInterface
{
    protected $basePath;

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return AppInterface|SiteApp
     */
    public function bootApp() {
        return new SiteApp($this->bootLaravel());
    }

    protected function bootLaravel() {
        $laravel = new Application($this->basePath);

        $laravel->instance('config', $config = $this->getIlluminateConfig($laravel));

        $laravel->register(WebServiceProvider::class);
//        $laravel->register(DatabaseServiceProvider::class);
//        $laravel->register(ServiceProvider::class);
//        $laravel->register(ExtensionServiceProvider::class);

//        $laravel->register(ServiceProvider::class);

        $laravel->boot();
        return $laravel;
    }

    protected function getIlluminateConfig($app) {

        $config = new ConfigRepository([]);
        $this->loadConfigurationFiles($app, $config);

        return $config;
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Config\Repository  $repository
     * @return void
     * @throws \Exception
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository)
    {
        $files = $this->getConfigurationFiles($app);

        if (! isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return array
     */
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        $configPath = realpath($app->configPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $configPath
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $configPath)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }

        return $nested;
    }
}
