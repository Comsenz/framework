<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
        $this->app->singleton('translator', function ($app) {
            $locale = $this->app->getLocale();

//           dd(compact('locale'));
            $translator = new Translator($locale, null, $this->app->storagePath().'/locale', $this->app->config('debug'));
            $translator->setFallbackLocales([$locale]);
            $translator->addLoader('yaml', new YamlFileLoader());

//           $translator->addResource('yaml', $file->, $locale);

            $directory = $this->app->resourcePath().'/lang/'.$locale;
            foreach (new DirectoryIterator($directory) as $file) {
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
