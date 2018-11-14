<?php

namespace Tests\Unit;

use Tests\TestCase;
use Elnooronline\LaravelSettings\Facades\Setting;

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
                'name' => 'Laravel'
            ]
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
                'name' => 'Laravel'
            ]
        ]);

        Setting::set('user', $collection);

        $this->assertEquals(Setting::get('user.name'), 'Ahmed Fathy');
        $this->assertEquals(Setting::get('user.tag.name'), 'Laravel');
    }
}