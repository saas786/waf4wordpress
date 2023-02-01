<?php
/**
 * General Settings View.
 *
 * Displays the general settings view (tab) on the settings page.
 */

namespace SzepeViktor\WordPress\Waf\Settings\Admin\Views;

use SzepeViktor\WordPress\Waf\Settings\Options;

/**
 * General settings view class.
 */
class General extends View {

    private $settings;

    /**
     * Returns the view name/ID.
     */
    public function name() {
        return 'general';
    }

    /**
     * Returns the internationalized, human-readable view label.
     */
    public function label() {
        return __( 'General', 'waf4wordpress' );
    }

    /**
     * Called on the `admin_init` hook and should be used to register plugin
     * settings via the Settings API.
     */
    public function register() {

        // Get the current plugin settings w/o the defaults.
        $this->settings = get_option( 'sz_waf_settings' );

        // Register the setting.
        register_setting( 'sz_waf_settings', 'sz_waf_settings', [ $this, 'validateSettings' ] );

        // Register sections and fields.
        add_action( 'sz_waf/settings/admin/view/general/register', [ $this, 'registerDefaultSections' ] );
        add_action( 'sz_waf/settings/admin/view/general/register', [ $this, 'registerDefaultFields' ] );
    }

    /**
     * Called on the `load-{$page}` hook when the view is booted. Use this
     * to add any actions or filters needed.
     */
    public function boot() {
        do_action( 'sz_waf/settings/admin/view/general/register' );
    }

    /**
     * Validates the settings.
     *
     * @param  array $input
     * @return array
     */
    function validateSettings( $settings ) {

        // Checkboxes.
        $settings['enable_core_events']   = ! empty( $settings['enable_core_events'] );
        $settings['enable_http_analyzer'] = ! empty( $settings['enable_http_analyzer'] );

        // Return the validated/sanitized settings.
        return $settings;
    }

    /**
     * Registers default settings sections.
     */
    public function registerDefaultSections() {

        $sections = [
            'toggle_components' => [
                'label'    => __( 'Components', 'waf4wordpress' ),
                'callback' => 'sectionToggleComponents',
            ],
        ];

        array_map( function( $name, $section ) {

            add_settings_section(
                $name,
                $section['label'],
                [ $this, $section['callback'] ],
                'sz_waf_settings'
            );

        }, array_keys( $sections ), $sections );
    }

    /**
     * Registers default settings fields.
     */
    public function registerDefaultFields() {

        $fields = [
            // Toggle Component fields.
            'core_events'   => [
                'label'    => __( 'Core Events', 'waf4wordpress' ),
                'callback' => 'fieldCoreEvents',
                'section'  => 'toggle_components',
            ],
            'http_analyzer' => [
                'label'    => __( 'HTTP Analyzer', 'waf4wordpress' ),
                'callback' => 'fieldHTTPAnalyzer',
                'section'  => 'toggle_components',
            ],
        ];

        array_map( function( $name, $field ) {

            add_settings_field(
                $name,
                $field['label'],
                [ $this, $field['callback'] ],
                'sz_waf_settings',
                $field['section']
            );

        }, array_keys( $fields ), $fields );
    }

    /**
     * Displays the Toggle Components section.
     */
    public function sectionToggleComponents() {
        ?>
        <p>
            <?php esc_html_e( 'Enable / Disable components.', 'waf4wordpress' ); ?>
        </p>
        <?php
    }

    /**
     * Displays the core events field.
     */
    public function fieldCoreEvents() {
        ?>

        <p>
            <label>
                <input type="checkbox" name="sz_waf_settings[enable_core_events]" value="true" <?php checked( Options::get( 'enable_core_events' ) ); ?> />
                <?php esc_html_e( 'Enable Core Events', 'waf4wordpress' ); ?>
            </label>
        </p>
        <p class="description">
            <?php esc_html_e( 'Enable the core events.', 'waf4wordpress' ); ?>
        </p>

        <?php
    }

    /**
     * Displays the http analyzer field.
     */
    public function fieldHTTPAnalyzer() {
        ?>

        <p>
            <label>
                <input type="checkbox" name="sz_waf_settings[enable_http_analyzer]" value="true" <?php checked( Options::get( 'enable_http_analyzer' ) ); ?> />
                <?php esc_html_e( 'Enable Http Analyzer', 'waf4wordpress' ); ?>
            </label>
        </p>

        <p class="description">
            <?php esc_html_e( 'Enable Http Analyzer.', 'waf4wordpress' ); ?>
        </p>

        <?php
    }

    /**
     * Renders the settings page.
     */
    public function template() {
        ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'sz_waf_settings' ); ?>
            <?php do_settings_sections( 'sz_waf_settings' ); ?>
            <?php submit_button( esc_attr__( 'Update Settings', 'waf4wordpress' ), 'primary' ); ?>
        </form>

        <?php
    }

}

