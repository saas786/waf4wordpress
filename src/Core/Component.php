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
     */
    public function is_active() {
        $config = plugin()->app->resolve( 'config' );

        return $config
            && array_key_exists( $this->component_setting_name, $config )
            && $config[ $this->component_setting_name ];
    }

}
