<?php
/**
 * Views Collection.
 *
 * Houses the collection of views in a single array-object.
 */

namespace SzepeViktor\WordPress\Waf\Settings\Admin\Views;

use SzepeViktor\WordPress\Waf\Core\Collection;

/**
 * Views class.
 */
class Views extends Collection {

    /**
     * Adds a new view to the collection.
     */
    public function put( $name, $value ) {

        $view = is_string( $value ) ? new $value() : $value;

        parent::put( $name, $view );
    }

}

