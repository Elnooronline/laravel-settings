<?php

namespace Elnooronline\LaravelSettings\Drivers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Elnooronline\LaravelSettings\Models\SettingModel;

class BaseSettingBuilder
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * All settings collection.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $settings;

    /**
     * BaseSettingBuilder constructor.
     */
    public function __construct()
    {
        if (! $this->supportCache()) {
            $this->forgetCache();
        }

        $this->setCollection();
    }

    protected function query()
    {
        $model = $this->getModelClassName();

        return $model::query();
    }

    /**
     * Set settings collection.
     *
     * @return void
     */
    protected function resetCollection()
    {
        $this->lang = null;
        $this->forgetCache();

        $this->setCollection();
    }

    protected function setCollection()
    {
        if ($this->supportCache()) {
            $this->settings = Cache::rememberForever('settings', function () {
                return $this->query()->get();
            });
        } else {
            $this->settings = $this->query()->get();
        }
    }

    /**
     * Get settings collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getCollection()
    {
        return Cache::get('settings') ?: $this->settings;
    }

    /**
     * Determine if the settings cache is supported.
     *
     * @return bool
     */
    protected function supportCache()
    {
        return ! ! Config::get('laravel_settings.cache');
    }

    /**
     * Forget the settings cache.
     *
     * @return mixed
     */
    public function forgetCache()
    {
        return Cache::forget('settings');
    }

    /**
     * Set the setting locale.
     *
     * @param null $lang
     * @return $this
     */
    public function lang($lang = null)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get the settings model class name.
     *
     * @return string
     */
    protected function getModelClassName()
    {
        return Config::get('laravel_setting.model_class', SettingModel::class);
    }
}