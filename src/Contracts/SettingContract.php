<?php

namespace Elnooronline\LaravelSettings\Contracts;

interface SettingContract
{
    public function get($key, $default = null);

    public function set($key, $value = null);
}