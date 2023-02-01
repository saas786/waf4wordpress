<?php

namespace SzepeViktor\WordPress\Waf;

use SzepeViktor\WordPress\Waf\Components\CoreEvents;
use SzepeViktor\WordPress\Waf\Components\HttpAnalyzer;
use SzepeViktor\WordPress\Waf\Core\Provider as BaseProvider;

/**
 * Plugin Provider.
 */
class Provider extends BaseProvider {

    /**
     * Register.
     */
    public function register() {
        $this->app->singleton( CoreEvents::class );
        $this->app->singleton( HttpAnalyzer::class );
    }

    /**
     * Boot.
     */
    public function boot() {
        $this->app->resolve( CoreEvents::class )->boot();
        $this->app->resolve( HttpAnalyzer::class )->boot();
    }

}
