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

    protected function query()
    {
        $model = $this->getModelClassName();

        return $model::query();
    }

    public function getModel($key = null)
    {
        $instance = $this->getCollection()->where('locale', $this->lang)->where('key', $key)->first();
        if (! $instance) {
            $model = $this->getModelClassName();

            return new $model;
        }

        return $instance;
    }

    /**
     * Set settings collection.
     *
     * @return void
     */
    protected function resetCollection()
    {
        $this->lang = null;
        $this->settings = null;
        $this->setCollection();
    }

    protected function setCollection()
    {
        if (! $this->settings) {
            $this->settings = $this->query()->get();
        }
    }

    /**
     * Get settings collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCollection()
    {
        $this->setCollection();

        return $this->settings;
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
        return Config::get('laravel_settings.model_class', SettingModel::class);
    }
}