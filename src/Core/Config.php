<?php

namespace SzepeViktor\WordPress\Waf\Core;

/**
 * Config class.
 */
class Config {

    /**
     * The application instance.
     *
     * @var    \SzepeViktor\WordPress\Waf\Core\App
     *
     * @access protected
     */
    protected $app;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Accepts the application and sets it to the `$app` property.
     *
     * @param \SzepeViktor\WordPress\Waf\Core\App $app
     * @param array                               $items
     */
    public function __construct( App $app, array $items = [] ) {
        $this->app = $app;

        $this->items = count( $items ) === 0 ? $this->load() : $items;
    }

    /**
     * Load config items.
     *
     * @return array
     */
    public function load() {

        $file = $this->app->resolve( 'config_path' ) . '/config.php';

        if ( ! file_exists( $file ) ) {
            $file = $this->app->resolve( 'path' ) . '/examples/config.php';
        }

        $items = include $file;

        return (array) $items;
    }

}
