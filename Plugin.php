<?php

namespace Initbiz\Selenium2tests;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'initbiz.selenium2tests::lang.plugin.name',
            'description' => 'initbiz.selenium2tests::lang.plugin.description',
            'author' => 'Initbiz',
            'icon' => 'oc-icon-check'
        ];
    }
}
