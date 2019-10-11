<?php


namespace Discuz\Api\Listeners;

use Discuz\Api\Events\ApiExceptionRegisterHandler;
use Discuz\Foundation\Application;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;

class AutoResisterApiExceptionRegisterHandler
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(ApiExceptionRegisterHandler $event) {

        $exceptions = $this->discoverExceptions((new Finder())->files()->in($this->discoverApiExceptionsWithin()), $this->app->basePath());

        foreach($exceptions as $exception) {
            $event->apiErrorHandler->registerHandler($exception->newInstance());
        }
    }

    protected function discoverExceptions($files, $basePath) {

        $exceptions = [];
        foreach($files as $file) {
            $class = new ReflectionClass($this->classFromFile($file, $basePath));
            if(!in_array(ExceptionHandlerInterface::class, $class->getInterfaceNames())) {
                continue;
            }
            $exceptions[] = $class;
        }
        return $exceptions;
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $basePath
     * @return string
     */
    protected function classFromFile(SplFileInfo $file, $basePath)
    {
        $class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return str_replace(
            [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );
    }

    protected function discoverApiExceptionsWithin() {
        $dir = $this->app->path('Api/Exceptions');

        return collect($dir)->reject(function($directory) {
            return ! is_dir($directory);
        })->toArray();
    }
}
