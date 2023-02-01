<?php

namespace SzepeViktor\WordPress\Waf\Core;

/**
 * Provider class.
 */
abstract class Provider {

    /**
     * The application instance.
     *
     * @var    \SzepeViktor\WordPress\Waf\Core\App
     *
     * @access protected
     */
    protected $app;

    /**
     * Accepts the application and sets it to the `$app` property.
     *
     * @param  \SzepeViktor\WordPress\Waf\Core\App $app
     * @return void
     */
    public function __construct( App $app ) {
        $this->app = $app;
    }

    /**
     * Boot.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register.
     *
     * @return void
     */
    public function register() {}

}
