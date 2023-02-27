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

	/**
	 * Determine if an item exists in the collection by key.
	 *
	 * @param  TKey|array<array-key, TKey> $key
	 * @return bool
	 */
	public function has( $key ) {
		$keys = is_array( $key ) ? $key : func_get_args();

		foreach ( $keys as $value ) {
			if ( ! array_key_exists( $value, $this->items ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get an item from the collection by key.
	 *
	 * @param  TKey                                  $key
	 * @param  TGetDefault|(\Closure(): TGetDefault) $default
	 * @return TValue|TGetDefault
	 *
	 * @template TGetDefault
	 */
	public function get( $key, $default = null ) {
		if ( array_key_exists( $key, $this->items ) ) {
			return $this->items[ $key ];
		}

		return $this->value( $default );
	}

	/**
	 * Return the default value of the given value.
	 *
	 * @param  mixed $value
	 * @return mixed
	 */
	public function value( $value, ...$args ) {
		return $value instanceof \Closure
			? $value( ...$args )
			: $value;
	}

	/**
	 * Get all of the items in the collection.
	 *
	 * @return array<TKey, TValue>
	 */
	public function all() {
		return $this->items;
	}

}
