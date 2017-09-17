<?php namespace InitBiz\Selenium2Tests;

use Backend;
use Event;
use System\Classes\PluginBase;

/**
 * Selenium2Tests Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Selenium2Tests',
            'description' => 'Add functionality to write tests in Selenium 2',
            'author'      => 'InitBiz',
            'icon'        => 'icon-gear'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
    }
}
