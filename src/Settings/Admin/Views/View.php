<?php
/**
 * Settings View Base.
 *
 * Abstract base class for creating views.
 */

namespace SzepeViktor\WordPress\Waf\Settings\Admin\Views;

/**
 * View class.
 */
abstract class View {

    /**
     * Returns the view name/ID.
     */
    abstract public function name();

    /**
     * Returns the internationalized, human-readable view label.
     */
    abstract public function label();

    /**
     * Called on the `admin_init` hook and should be used to register plugin
     * settings via the Settings API.
     */
    public function register() {}

    /**
     * Called on the `load-{$page}` hook when the view is booted. Use this
     * to add any actions or filters needed.
     */
    public function boot() {}

    /**
     * Called when the page's HTML is output for the view.
     */
    public function template() {}

}

