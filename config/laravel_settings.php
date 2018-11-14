<?php
return [
    /**
     * At the moment the supported drivers only database.
     */
    'driver' => env('SETTINGS_DRIVER', 'database'),

    /**
     * The model of the settings table.
     * if you want to override the model you should extend your settings model from the package model class.
     *
     *   'model_class' => \App\CustomSetting::class,
     *
     *   class CustomSetting extends \Elnooronline\LaravelSettings\Models\SettingModel
     *   {
     *      ...
     *   }
     */
    'model_class' => \Elnooronline\LaravelSettings\Models\SettingModel::class,
];
