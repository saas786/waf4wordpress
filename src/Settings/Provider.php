<?php
/**
 * Settings Provider.
 */

namespace SzepeViktor\WordPress\Waf\Settings;

use SzepeViktor\WordPress\Waf\Core\Provider as BaseProvider;
use SzepeViktor\WordPress\Waf\Settings\Admin\OptionsPage;
use SzepeViktor\WordPress\Waf\Settings\Admin\Views\Views;

/**
 * Settings Provider class.
 */
class Provider extends BaseProvider {

    /**
     * Register.
     */
    public function register() {

        $this->app->singleton( Views::class );

        $this->app->singleton( OptionsPage::class, fn() => new OptionsPage(
            'sz-waf-settings',
            $this->app->resolve( Views::class ),
            [
                'label'      => __( 'WAF Settings', 'waf4wordpress' ),
                'capability' => 'edit_theme_options',
            ]
        ) );
    }

    /**
     * Boot.
     */
    public function boot() {

        if ( is_admin() ) {
            $this->app->resolve( OptionsPage::class )->boot();
        }
    }

}

