<?php

namespace SzepeViktor\WordPress\Waf;

/**
 * Wrapper for the plugin instance.
 *
 * @since  0.0.2
 * @return \SzepeViktor\WordPress\Waf\Plugin|null
 */
function plugin(): ?Plugin {
    static $instance = null;

    if ( is_null( $instance ) ) {
        $instance = new Plugin( VS_WAF_PLUGIN_DIR, VS_WAF_PLUGIN_URL );
    }

    return $instance;
}

// Boot the plugin.
if ( ! did_action( 'sz_wafwordpress_booted' ) ) {
    plugin()->boot();
}

/**
 * Registers the plugin activation callback.
 *
 * @since  0.0.2
 * @return void
 */
register_activation_hook( VS_WAF_PLUGIN_FILE, [ plugin(), 'activate' ] );
