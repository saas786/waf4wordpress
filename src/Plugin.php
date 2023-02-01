<?php
/**
 * Primary plugin class.
 *
 * Launches the plugin components and acts as a simple container.
 *
 * @package    SzepeViktor\WordPress\Waf
 *
 * @author     Viktor Szépe <viktor@szepe.net>
 * @copyright  Copyright (c) 2023, Viktor Szépe
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace SzepeViktor\WordPress\Waf;

use SzepeViktor\WordPress\Waf\Core\App;
use SzepeViktor\WordPress\Waf\Core\Config;
use SzepeViktor\WordPress\Waf\Provider as MainProvider;
use SzepeViktor\WordPress\Waf\Settings\Provider as SettingsProvider;

/**
 * Plugin class.
 *
 * @since  0.0.2
 */
class Plugin {

    /**
     * The application instance.
     *
     * @var    \SzepeViktor\WordPress\Waf\Core\App
     *
     * @access protected
     */
    public $app;

    /**
     * Stores the plugin directory path.
     *
     * @since  0.0.2
     * @var    string
     */
    protected $path;

    /**
     * Stores the plugin directory URI.
     *
     * @since  0.0.2
     * @var    string
     */
    protected $uri;

    /**
     * Stores the plugin config directory path.
     *
     * @since  0.0.2
     * @var    string
     */
    protected $config_path;

    /**
     * Sets up the object properties.
     *
     * @since  0.0.2
     * @param  string $path  Plugin directory path.
     * @param  string $uri   Plugin directory URI.
     * @return void
     */
    public function __construct( $path, $uri ) {

        $this->path = untrailingslashit( $path );
        $this->uri  = untrailingslashit( $uri );

        $this->config_path = WP_CONTENT_DIR . '/waf4wordpress';

        // Register the plugin.
        $this->register();
    }

    public function register() {
        $this->app = new App();

        $this->app->instance( 'path', $this->path );
        $this->app->instance( 'uri', $this->uri );
        $this->app->instance( 'config_path', $this->config_path );

        $this->app->instance( 'config', static fn() => new Config( $this->app, [] ) );

        $this->app->provider( SettingsProvider::class );
        $this->app->provider( MainProvider::class );
    }

    /**
     * Runs necessary code when first activating the plugin.
     *
     * @since  0.0.2
     * @return void
     */
    public function activate() {

        $file_name = '/config.php';

        $src  = $this->path . '/examples';
        $dest = $this->config_path;

        if ( ! is_dir( $dest ) ) {
            mkdir( $dest, 0777, true );
        }

        $src_file  = $src . $file_name;
        $dest_file = $dest . $file_name;

        // Please create a backup copy if file already exists.
        if ( file_exists( $dest_file ) ) {
            copy( $dest_file, $dest_file . date( 'Y-m-d-H-i-s' ) . '.bak' );
        }

        copy( $src_file, $dest_file );
    }

    /**
     * Bootstraps the components.
     *
     * @since  0.0.2
     * @return void
     * @throws \Exception
     */
    public function boot() {
        $this->app->boot();
    }

    /**
     * Returns the plugin path.
     *
     * @since  0.0.2
     * @param  string $file
     * @return string
     */
    public function path( $file = '' ) {

        $file = ltrim( $file, '/' );

        return $file ? $this->path . "/{$file}" : $this->path;
    }

    /**
     * Returns the plugin URI.
     *
     * @since  0.0.2
     * @param  string $file
     * @return string
     */
    public function uri( $file = '' ) {

        $file = ltrim( $file, '/' );

        return $file ? $this->uri . "/{$file}" : $this->uri;
    }

}
