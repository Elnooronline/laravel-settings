<?php

namespace Elnooronline\LaravelSettings\Drivers;

use Illuminate\Support\Facades\DB;
use Elnooronline\LaravelSettings\Contracts\SettingContract;

class DatabaseBuilder extends BaseSettingBuilder implements SettingContract
{
    /**
     * Get the given setting by key.
     *
     * @param $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (strpos($key, '.') !== false) {
            $array = array_dot($this->get(($keys = explode('.', $key))[0]));
            if (array_key_exists($k = preg_replace('/^([a-zA-Z0-9_-]+\.)/', '', $key), $array)) {
                return array_get($array, $k);
            }
        }
        $instance = $this->getModel($key);

        $value = optional($instance)->value;

        $value = $value && $this->isSerialized($value) ? unserialize($value) : $value;

        return $value ?: $default;
    }

    /**
     * Update or set setting value.
     *
     * @param $key
     * @param null $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function set($key, $value = null)
    {
        if (is_array($value) || is_object($value)) {
            $value = serialize($value);
        }

        $model = $this->getModelClassName();
        if (is_array($key)) {
            foreach ($key as $k => $val) {
                $model::updateOrCreate([
                    'key' => $k,
                    'locale' => $this->lang,
                ], [
                    'value' => $val,
                ]);
            }
        } else {
            $model::updateOrCreate([
                'key' => $key,
                'locale' => $this->lang,
            ], [
                'value' => $value,
            ]);
        }

        $this->resetCollection();

        return $this->getModel($key);
    }

    /**
     * Delete the specified setting instance.
     *
     * @param $key
     */
    public function forget($key)
    {
        $table = $this->query()->getModel()->getTable();

        DB::table($table)->where('locale', $this->lang)->where('key', $key)->delete();

        $this->resetCollection();
    }

    /**
     * Delete the specified setting instance for all languages.
     *
     * @param $key
     */
    public function forgetAll($key)
    {
        $table = $this->query()->getModel()->getTable();

        DB::table($table)->where('key', $key)->delete();

        $this->resetCollection();
    }

    /**
     * Determine whether the key is already exists.
     *
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return ! ! $this->first($key);
    }

    /**
     * Determine whether the key is not exists.
     *
     * @param $key
     * @return bool
     */
    public function hasNot($key)
    {
        return ! $this->has($key);
    }

    /**
     * Get the key instanse.
     *
     * @param $key
     * @return bool
     */
    public function first($key)
    {
        return $this->getCollection()->where('locale', $this->lang)->where('key', $key)->first();
    }

    /**
     * Determine whether the key is already exists and the value is not dirty.
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function is($key, $value)
    {
        return $this->has($key) && $this->first($key)->value == $value;
    }

    /**
     * Determine whether the key is already exists and the value is dirty.
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function isNot($key, $value)
    {
        return $this->has($key) && $this->first($key)->value != $value;
    }

    private function isSerialized($str)
    {
        return ($str == serialize(false) || @unserialize($str) !== false);
    }
}