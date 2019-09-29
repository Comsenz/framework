<?php

namespace Discuz\Locale;

use DirectoryIterator;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Illuminate\Contracts\Translation\Translator as ContractsTranslator;

class LocaleServiceProvider extends ServiceProvider
{

    public function register()
    {
       $this->app->singleton('translator', function($app) {
           $locale = $this->app->getLocale();

//           dd(compact('locale'));
           $translator = new Translator($locale, null, $this->app->storagePath().'/locale', $this->app->config('debug'));
           $translator->setFallbackLocales([$locale]);
           $translator->addLoader('yaml', new YamlFileLoader());

//           $translator->addResource('yaml', $file->, $locale);

           $directory = $this->app->resourcePath().'/lang/'.$locale;
           foreach(new DirectoryIterator($directory) as $file) {
               if ($file->isFile() && in_array($file->getExtension(), ['yml', 'yaml'])) {
                   $translator->addResource('yaml', $file->getPathname(), $locale);
               }
           }

           return $translator;
       });

       $this->app->alias('translator', Translator::class);
       $this->app->alias('translator', ContractsTranslator::class);
       $this->app->alias('translator', TranslatorInterface::class);
    }
}
