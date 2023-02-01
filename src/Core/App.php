<?php

namespace SzepeViktor\WordPress\Waf\Core;

class App extends Container {

    /**
     * All of the registered service providers.
     *
     * @var \SzepeViktor\WordPress\Waf\Core\Provider[]
     */
    protected $serviceProviders = [];

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * Create a new application instance.
     *
     * @return void
     */
    public function __construct() {
        $this->registerBaseBindings();
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings() {
        static::setInstance( $this );

        $this->instance( 'app', $this );
        $this->instance( Container::class, $this );
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot() {
        if ( $this->isBooted() ) {
            return;
        }

        array_walk($this->serviceProviders, function ( $p ) {
            $this->bootProvider( $p );
        });

        $this->booted = true;
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted() {
        return $this->booted;
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \SzepeViktor\WordPress\Waf\Core\Provider|string $provider
     * @param  bool                                            $force
     * @return \SzepeViktor\WordPress\Waf\Core\Provider
     */
    public function provider( $provider, $force = false ) {
        if ( ( $registered = $this->getProvider( $provider ) ) && ! $force ) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if ( is_string( $provider ) ) {
            $provider = $this->resolveProvider( $provider );
        }

        $provider->register();

        // If there are bindings / singletons set as properties on the provider we
        // will spin through them and register them with the application, which
        // serves as a convenience layer while registering a lot of bindings.
        if ( property_exists( $provider, 'bindings' ) ) {
            foreach ( $provider->bindings as $key => $value ) {
                $this->bind( $key, $value );
            }
        }

        if ( property_exists( $provider, 'singletons' ) ) {
            foreach ( $provider->singletons as $key => $value ) {
                $key = is_int( $key ) ? $value : $key;

                $this->singleton( $key, $value );
            }
        }

        $this->markAsRegistered( $provider );

        // If the application has already booted, we will call this boot method on
        // the provider class so it has an opportunity to do its boot logic and
        // will be ready for any usage by this developer's application logic.
        if ( $this->isBooted() ) {
            $this->bootProvider( $provider );
        }

        return $provider;
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  \SzepeViktor\WordPress\Waf\Core\Provider|string $provider
     * @return \SzepeViktor\WordPress\Waf\Core\Provider|null
     */
    public function getProvider( $provider ) {
        return array_values( $this->getProviders( $provider ) )[0] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param  \SzepeViktor\WordPress\Waf\Core\Provider|string $provider
     * @return array
     */
    public function getProviders( $provider ) {
        $name = is_string( $provider ) ? $provider : get_class( $provider );

        return static::where( $this->serviceProviders, static fn( $value ) => $value instanceof $name );
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string $provider
     * @return \SzepeViktor\WordPress\Waf\Core\Provider
     */
    public function resolveProvider( $provider ) {
        return new $provider( $this );
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  \SzepeViktor\WordPress\Waf\Core\Provider $provider
     * @return void
     */
    protected function markAsRegistered( $provider ) {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[ get_class( $provider ) ] = true;
    }

    /**
     * Filter the array using the given callback.
     *
     * @param  array    $array
     * @param  callable $callback
     * @return array
     */
    public static function where( $array, callable $callback ) {
        return array_filter( $array, $callback, ARRAY_FILTER_USE_BOTH );
    }

    /**
     * Boot the given service provider.
     *
     * @param  \SzepeViktor\WordPress\Waf\Core\Provider $provider
     * @return void
     */
    protected function bootProvider( Provider $provider ) {
        if ( method_exists( $provider, 'boot' ) ) {
            call_user_func( [ $provider, 'boot' ] );
        }
    }

}
