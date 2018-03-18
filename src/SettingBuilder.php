<?php

namespace Elnooronline\LaravelSettings;

use Illuminate\Support\Facades\Config;
use Elnooronline\LaravelSettings\Drivers\JsonBuilder;
use Elnooronline\LaravelSettings\Drivers\DatabaseBuilder;

class SettingBuilder
{
    /**
     * The supported settings drtivers.
     *
     * @var array
     */
    private $drivers = [
        'database' => DatabaseBuilder::class,
    ];

    /**
     * SettingBuilder constructor.
     */
    private function __construct()
    {
        //
    }

    /**
     * Get the settings instance.
     *
     * @return mixed
     */
    public static function getInstance()
    {
        return (new static)->driver(Config::get('laravel_settings.driver'));
    }

    /**
     * Get the driver instance.
     *
     * @param $driver
     * @return \Elnooronline\LaravelSettings\Contracts\SettingContract
     */
    public function driver($driver)
    {
        $instance = $this->drivers[$driver];

        return new $instance;
    }
}