<?php

namespace Elnooronline\LaravelSettings\Models\Traits;

use Elnooronline\LaravelSettings\Facades\Setting;
use Elnooronline\LaravelSettings\Models\SettingModel;

trait HasSettings
{
    /**
     * Get the settings instance of the model.
     *
     * @return \Elnooronline\LaravelSettings\SettingBuilder
     */
    public function settings()
    {
        return Setting::for($this)->lang();
    }

    /**
     * Get all setting of the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function getSettings()
    {
        return $this->morphMany(SettingModel::class, 'model');
    }
}