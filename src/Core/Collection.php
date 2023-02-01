<?php

namespace SzepeViktor\WordPress\Waf\Core;

use ArrayObject;

class Collection extends ArrayObject {

    /**
     * Add an item.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function put( $name, $value ) {
        $this->offsetSet( $name, $value );
    }

    /**
     * Removes an item.
     *
     * @param  string $name
     * @return void
     */
    public function forget( $name ) {
        $this->offsetUnset( $name );
    }

    /**
     * Checks if an item exists.
     *
     * @param  string $name
     * @return bool
     */
    public function has( $name ) {
        return $this->offsetExists( $name );
    }

    /**
     * Returns an item.
     *
     * @param  string $name
     * @return mixed
     */
    public function get( $name ) {
        return $this->offsetGet( $name );
    }

    /**
     * Returns the collection of items.
     *
     * @return array
     */
    public function all() {
        return $this->getArrayCopy();
    }

}
