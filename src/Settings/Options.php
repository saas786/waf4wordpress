<?php
/**
 * Options Helper.
 *
 * This is an options helper class for quickly getting plugin options.
 */

namespace SzepeViktor\WordPress\Waf\Settings;

/**
 * Options class.
 */
class Options {

    /**
     * Gets a plugin option by name. If name is omitted, returns all options.
     *
     * @param  string $name
     * @return mixed
     */
    public static function get( $name = '', $default = null ) {

        $defaults = static::defaults();
        $settings = wp_parse_args( get_option( 'sz_waf_settings', $defaults ), $defaults );

        if ( ! $name ) {
            return $settings;
        }

        return $settings[ $name ] ?? null;
    }

    /**
     * Returns an array of all default options.
     *
     * @return array
     */
    public static function defaults() {
        return apply_filters( 'wz_waf/settings/options/defaults', [
            'CoreEvents'   => false,
            'HttpAnalyzer' => false,
        ] );
    }

}

