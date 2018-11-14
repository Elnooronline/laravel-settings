<?php

namespace Elnooronline\LaravelSettings\Drivers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Elnooronline\LaravelSettings\Models\SettingModel;
use Prophecy\Exception\Doubler\MethodNotFoundException;

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
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $prefixMethods = [];

    /**
     * @var array
     */
    protected $conditions = [];

    /**
     * @param $name
     * @param $arguments
     * @return $this
     * @throws \Prophecy\Exception\Doubler\MethodNotFoundException
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->prefixMethods)) {
            $this->conditions[$name] = array_first($arguments);
            return $this;
        }
        throw new MethodNotFoundException("{$name}() method not found!", __CLASS__, $name);
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
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

    /**
     * Register custom prefix method.
     *
     * @param array|string $method
     * @return $this
     */
    public function registerPrefixMethod($method)
    {
        if (is_array($method)) {
            $this->prefixMethods = $method;

            return $this;
        }

        $this->prefixMethods = func_get_args();

        return $this;
    }

    /**
     * Set the global conditions.
     *
     * @param array $conditions
     * @return $this
     */
    public function setConditions(array $conditions = [])
    {
        $this->conditions = $conditions;
        return $this;
    }

    protected function supportPrefix(&$key)
    {
        $this->withPrefix();

        if ($this->prefix) {
            $key = "{$this->prefix}{$key}";
        }
    }

    public function hasPrefix($method)
    {
        preg_match("/($method)__/", $this->prefix, $matches);

        return isset($matches[1]) && $matches[1] == $method;
    }

    public function withPrefix()
    {

        foreach ($this->conditions as $k => $condition) {
            if ($this->hasPrefix($k)) {
                $this->prefix = preg_replace("/($k)__([a-zA-Z0-9]+)/", "$1__$condition", $this->prefix);
            }else{
                $this->prefix = "_{$k}__{$condition}_{$this->prefix}";
            }
        }
    }

}