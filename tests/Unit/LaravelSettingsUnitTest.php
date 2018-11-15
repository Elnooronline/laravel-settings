<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Elnooronline\LaravelSettings\SettingBuilder;
use Elnooronline\LaravelSettings\Facades\Setting;
use Elnooronline\LaravelSettings\Models\SettingModel;

class LaravelSettingsUnitTest extends TestCase
{
    /** @test */
    public function it_can_set_and_get_data()
    {
        Setting::set('name', 'Ahmed Fathy');
        Setting::set('phone', '021207687151');

        $this->assertEquals(Setting::get('phone'), '021207687151');
        $this->assertEquals(Setting::get('name'), 'Ahmed Fathy');
    }

    /** @test */
    public function it_returns_default_value_if_the_key_does_not_exists()
    {
        $this->assertEquals(Setting::get('UndefindKey', 'FooBar'), 'FooBar');
    }

    /** @test */
    public function it_returns_value_of_multi_level_array()
    {
        $array = [
            'name' => 'Ahmed Fathy',
            'phone' => '021207687151',
            'tag' => [
                'name' => 'Laravel',
            ],
        ];
        Setting::set('user', $array);

        $this->assertEquals(Setting::get('user.name'), 'Ahmed Fathy');
        $this->assertEquals(Setting::get('user.tag.name'), 'Laravel');
    }

    /** @test */
    public function it_returns_value_of_multi_level_collection()
    {
        $collection = collect([
            'name' => 'Ahmed Fathy',
            'phone' => '021207687151',
            'tag' => [
                'name' => 'Laravel',
            ],
        ]);

        Setting::set('user', $collection);

        $this->assertEquals(Setting::get('user.name'), 'Ahmed Fathy');
        $this->assertEquals(Setting::get('user.tag.name'), 'Laravel');
    }

    /** @test */
    public function it_returns_unique_value_of_localed_data()
    {
        Setting::lang('en')->set('language', 'English');
        Setting::lang('ar')->set('language', 'Arabic');

        $this->assertEquals(Setting::lang('en')->get('language'), 'English');
        $this->assertEquals(Setting::lang('ar')->get('language'), 'Arabic');

        Setting::lang('en')->forget('language');
        Setting::lang('ar')->forget('language');

        Setting::set('language:en', 'English');
        Setting::set('language:ar', 'Arabic');

        $this->assertEquals(Setting::lang('en')->get('language'), 'English');
        $this->assertEquals(Setting::lang('ar')->get('language'), 'Arabic');

        $this->assertEquals(Setting::get('language:en'), 'English');
        $this->assertEquals(Setting::get('language:ar'), 'Arabic');

        Setting::lang('en')->set('language', 'English');

        $this->assertEquals(SettingModel::where(['locale' => 'en', 'key' => 'language'])->count(), 1);
    }

    /** @test */
    public function it_determine_if_the_value_exists()
    {
        Setting::set('name', 'Ahmed');

        $this->assertTrue(Setting::has('name'));

        Setting::lang('en')->set('language', 'English');

        $this->assertTrue(Setting::lang('en')->has('language'));
        $this->assertTrue(Setting::has('language:en'));
    }

    /** @test */
    public function it_can_deleted_the_specific_key()
    {
        Setting::set('name', 'Ahmed');

        $this->assertTrue(Setting::has('name'));

        Setting::forget('name');

        $this->assertFalse(Setting::has('name'));

        $this->assertDatabaseMissing('settings', [
            'key' => 'name',
        ]);

        Setting::lang('en')->set('name', 'Ahmed');
        Setting::lang('ar')->set('name', 'احمد');

        $this->assertTrue(Setting::lang('en')->has('name'));
        $this->assertTrue(Setting::lang('ar')->has('name'));

        Setting::lang('en')->forget('name');

        $this->assertFalse(Setting::lang('en')->has('name'));
        $this->assertTrue(Setting::lang('ar')->has('name'));

        $this->assertDatabaseMissing('settings', [
            'key' => 'name',
            'locale' => 'en',
        ]);
        Setting::forget('name:ar');
        $this->assertFalse(Setting::lang('ar')->has('name'));
        $this->assertFalse(Setting::has('name:ar'));

        $this->assertDatabaseMissing('settings', [
            'key' => 'name',
            'locale' => 'ar',
        ]);
    }

    /** @test */
    public function it_supported_the_key_prefix_condition()
    {
        Config::set('laravel-settings.prefix_methods', ['country', 'foo']);

        Setting::country('us');
        Setting::foo('bar');

        Setting::set('name', 'Ahmed');

        $this->assertEquals(Setting::get('name'), 'Ahmed');
        $this->assertDatabaseHas('settings', [
            'key' => '_foo__bar__country__us_name',
        ]);
        $this->assertEquals(Setting::all()->count(), 1);
        Setting::country('eg');
        Setting::foo('baz');

        Setting::set('name', 'Omar');
        $this->assertDatabaseHas('settings', [
            'key' => '_foo__baz__country__eg_name',
        ]);
        $this->assertEquals(Setting::all()->count(), 2);

        Config::set('laravel-settings.prefix_methods', []);

        Setting::registerPrefixMethod('country');

        $this->assertInstanceOf(SettingBuilder::class, Setting::country('us'));
    }

    public function test_config_file()
    {
        $this->assertEquals(Config::get('laravel-settings.model_class'), SettingModel::class);
    }

    /** @test */
    public function it_can_set_global_prefix_conditions()
    {
        Config::set('laravel-settings.global_conditions', [
            'country' => 'iq',
        ]);

        Setting::set('title', 'Website');

        $this->assertDatabaseHas('settings', [
            'key' => '_country__iq_title',
            'value' => 'Website',
        ]);
    }

    /** @test */
    public function it_can_clear_prefix_when_using_prefix_conditions()
    {
        Config::set('laravel-settings.global_conditions', [
            'country' => 'iq',
        ]);

        Setting::withoutPrefix();

        Setting::set('title', 'Website');

        $this->assertDatabaseHas('settings', [
            'key' => 'title',
            'value' => 'Website',
        ]);
    }

    /** @test */
    public function it_returns_model_after_set_value()
    {
        $this->assertInstanceOf(SettingModel::class, Setting::set('foo', 'bar'));
    }

    /** @test */
    public function it_returns_model_when_get_model()
    {
        Setting::set('foo', 'bar');

        $this->assertInstanceOf(SettingModel::class, Setting::getModel('foo'));

        Setting::lang('en')->set('title', 'Website');

        $this->assertEquals(Setting::getModel('title:en')->value, 'Website');

        Setting::forgetAll('title');

        Config::set('laravel-settings.global_conditions', [
            'country' => 'iq',
        ]);

        Setting::set('title', 'Website');

        $this->assertEquals(Setting::getModel('title')->value, 'Website');
    }
}