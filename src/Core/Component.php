<?php

namespace SzepeViktor\WordPress\Waf\Core;

use function SzepeViktor\WordPress\Waf\plugin;

/**
 * Component class.
 */
abstract class Component {

    /**
     * Boot.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Is active?.
     *
     * @return bool
     * @throws \Exception
     */
    public function is_active() {
        $config = $this->get_config();

        return $config['is_active'] ?: false;
    }

    /**
     * Get configs.
     *
     * @throws \Exception
     */
    public function get_configs() {
        return plugin()->app->resolve( 'config' );
    }

    /**
     * Get config.
     *
     * @throws \Exception
     */
    public function get_config() {
        $config = $this->get_configs();

        if ( ! $config || ! $config->has( $this->component_setting_name ) ) {
            return [];
        }

        return new Collection( $config->get( $this->component_setting_name ) );
    }

}
