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

namespace Discuz\Foundation\Support;

use Discuz\Foundation\Application;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class Decomposer
{
    protected $app;

    protected $request;

    public function __construct(Application $app, ServerRequestInterface $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Get the Composer file contents as an array
     * @return array
     */
    public static function getComposerArray()
    {
        $json = file_get_contents(base_path('composer.json'));
        return json_decode($json, true);
    }

    /**
     * Get Installed packages & their Dependencies
     *
     * @param $packagesArray
     * @return array
     */
    public static function getPackagesAndDependencies($packagesArray)
    {
        foreach ($packagesArray as $key => $value) {
            $packageFile = base_path("/vendor/{$key}/composer.json");
            if ($key !== 'php' && file_exists($packageFile)) {
                $json2 = file_get_contents($packageFile);
                $dependenciesArray = json_decode($json2, true);
                $dependencies = array_key_exists('require', $dependenciesArray) ? $dependenciesArray['require'] : 'No dependencies';
                $devDependencies = array_key_exists('require-dev', $dependenciesArray) ? $dependenciesArray['require-dev'] : 'No dependencies';
                $packages[] = [
                    'name' => $key,
                    'version' => $value,
                    'dependencies' => $dependencies,
                    'dev-dependencies' => $devDependencies
                ];
            }
        }
        return $packages;
    }

    /**
     * Get the laravel app's size
     *
     * @param $dir
     * @return int
     */
    protected static function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : self::folderSize($each);
        }
        return $size;
    }

    /**
     * Format the app's size in correct units
     *
     * @param $bytes
     * @return string
     */
    protected static function sizeFormat($bytes)
    {
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;
        $tb = $gb * 1024;
        if (($bytes >= 0) && ($bytes < $kb)) {
            return $bytes . ' B';
        } elseif (($bytes >= $kb) && ($bytes < $mb)) {
            return ceil($bytes / $kb) . ' KB';
        } elseif (($bytes >= $mb) && ($bytes < $gb)) {
            return ceil($bytes / $mb) . ' MB';
        } elseif (($bytes >= $gb) && ($bytes < $tb)) {
            return ceil($bytes / $gb) . ' GB';
        } elseif ($bytes >= $tb) {
            return ceil($bytes / $tb) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }

    public function getSiteinfo()
    {
        //获取系统基本信息
        $pdo = app('db')->getPdo();

        $composerArray = self::getComposerArray();
        $packages = self::getPackagesAndDependencies($composerArray['require']);

        $server = $this->request->getServerParams();

        $ssl_installed = !empty(Arr::get($server, 'HTTPS') && Arr::get($server, 'HTTPS') != 'off');

        return [
            'version' => $this->app::VERSION,
            'php_version' => PHP_OS.' / PHP v'.PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'],
            'server_os' => php_uname(),
            'db' => $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME). '/'. $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),
            'database_connection_name' => config('database.default'),
            'ssl_installed' => $ssl_installed,
            'cache_driver' => config('cache.default'),
            'upload_size' => ini_get('upload_max_filesize'),
            'db_size' => self::sizeFormat($this->tables_size($pdo)),
            'timezone' => $this->app->config('timezone'),
            'debug_mode' => $this->app->config('debug'),
            'storage_dir_writable' => is_writable(base_path('storage')),
            'cache_dir_writable' => is_writable(storage_path('cache')),
            'app_size' => self::sizeFormat(self::folderSize(app_path())),
            'packages' => $packages
        ];
    }

    protected function tables_size(\PDO $pdo)
    {
        static $dbsize = 0;
        $query = $pdo->query("SHOW TABLE STATUS LIKE '".$this->app->make('db')->getTablePrefix()."%'");
        while ($table = $query->fetch()) {
            $dbsize += $table['Data_length'] + $table['Index_length'];
        }
        return $dbsize;
    }
}
