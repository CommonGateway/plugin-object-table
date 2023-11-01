<?php

namespace CommonGateway\ObjectTable\Foundation;

class Plugin
{
    /**
     * Path to the root of the plugin.
     *
     * @var string $rootPath
     */
    protected $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * Boot plugin classes.
     *
     * @return void
     */
    public function boot(): void
    {
        new \CommonGateway\ObjectTable\Classes\ObjectTablePluginShortcodes($this);
        new \CommonGateway\ObjectTable\Classes\ObjectTablePluginAdminSettings();
    }

    /**
     * Return root path of plugin.
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }
}
