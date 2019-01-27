<?php

namespace Elnooronline\LaravelSettings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Setting
 *
 * @method static \Illuminate\Database\Eloquent\Model set($key, $value = null)
 * @method static mixed get($key, $default = null)
 * @method static \Elnooronline\LaravelSettings\Models\SettingModel getModel($key = null)
 * @method static \Illuminate\Database\Eloquent\Collection getCollection()
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static void forget($key)
 * @method static void forgetAll($key)
 * @method static \Elnooronline\LaravelSettings\SettingBuilder withoutPrefix()
 * @method static \Elnooronline\LaravelSettings\SettingBuilder setConditions(array $conditions = [])
 * @method static \Elnooronline\LaravelSettings\SettingBuilder lang($lang = null)
 * @method static \Elnooronline\LaravelSettings\SettingBuilder registerPrefixMethod($method)
 * @method static \Elnooronline\LaravelSettings\SettingBuilder for($model)
 * @package Elnooronline\LaravelSettings\Facades
 */
class Setting extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'settings';
    }
}