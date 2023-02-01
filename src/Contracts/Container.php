<?php

namespace SzepeViktor\WordPress\Waf\Contracts;

/**
 * Container interface.
 */
interface Container {

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string $abstract
     * @return bool
     */
    public function bound( $abstract );

    /**
     * Register a binding with the container.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @param  bool                 $shared
     * @return void
     */
    public function bind( $abstract, $concrete = null, $shared = false );

}
