<?php
/**
 * Options Page Class.
 *
 * Displays the plugin settings page in the admin.
 */

namespace SzepeViktor\WordPress\Waf\Settings\Admin;

use function SzepeViktor\WordPress\Waf\plugin;

/**
 * Options page class.
 */
class OptionsPage {

    /**
     * Settings page name/slug.
     *
     * @var    string
     */
    protected $name;

    /**
     * Collection of views to display.
     *
     * @var \SzepeViktor\WordPress\Waf\Settings\Admin\Views\Views
     */
    protected $views;

    /**
     * Internationalized text label for the page.
     *
     * @var    string
     */
    protected $label = '';

    /**
     * Required capability for accessing the page.
     *
     * @var    string
     */
    protected $capability = 'edit_theme_options';

    /**
     * The settings page defined by WordPress.
     *
     * @var    string
     */
    protected $page = '';

    /**
     * Creates the settings page object.
     *
     * @param  string                                                $name
     * @param \SzepeViktor\WordPress\Waf\Settings\Admin\Views\Views $views
     * @param  array                                                 $args
     * @return void
     */
    public function __construct( $name, Views\Views $views, array $args = [] ) {

        foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

            if ( isset( $args[ $key ] ) ) {
                $this->$key = $args[ $key ];
            }
        }

        $this->name  = $name;
        $this->views = $views;
    }

    /**
     * Bootstraps the options page.
     *
     * @return void
     */
    public function boot() {
        add_action( 'admin_menu', [ $this, 'adminMenu' ] );
        add_action( 'update_option_sz_waf_settings', [ $this, 'optionsSaved' ], 10, 2 );
        add_filter( 'plugin_action_links_' . VS_WAF_PLUGIN_BASENAME, [ $this, 'pluginActionLinks' ] );
    }

    /**
     * Handler for the individual setting updated hook.
     *
     * @param mixed $old_value The old option value.
     * @param mixed $value The new option value.
     * @throws \Exception
     */
    public function optionsSaved( $old_value, $value ) {

            $config_path = plugin()->app->resolve( 'config_path' );

            $file_path     = $config_path . '/config.php';
            $file_contents = "<?php\nreturn " . var_export( $value, true ) . ';';

            file_put_contents( $file_path, $file_contents );
    }

    /**
     * Adds the settings page to WordPress.
     *
     * @return void
     */
    public function adminMenu() {

        $this->page = add_options_page(
            esc_html( $this->label ),
            esc_html( $this->label ),
            $this->capability,
            $this->name,
            [ $this, 'template' ]
        );

        if ( $this->page ) {

            add_action( 'admin_init', [ $this, 'init' ] );
            add_action( "load-{$this->page}", [ $this, 'load' ] );
        }
    }

    /**
     * Show action links on the plugin screen.
     *
     * @param mixed $links Plugin Action links.
     * @return array
     */
    public static function pluginActionLinks( $links ) {
        $action_links = array(
            'settings' => '<a href="' . admin_url( 'options-general.php?page=sz-waf-settings' ) . '" aria-label="' . esc_attr__( 'View WAF4WordPress settings', 'waf4wordpress' ) . '">' . esc_html__( 'Settings', 'waf4wordpress' ) . '</a>',
        );

        return array_merge( $action_links, $links );
    }

    /**
     * Called on `admin_init` to register views.
     *
     * @return void
     */
    public function init() {

        $this->views->put( 'general', Views\General::class );

        $this->registerViews();
    }

    /**
     * Called on `load-{$this->page}`. Primarily for booting the current view.
     *
     * @return void
     */
    public function load() {

        // Print custom styles.
        add_action( 'admin_head', array( $this, 'print_styles' ) );

        // Get the current view and boot it.
        $view = $this->currentView();

        if ( $view ) {
            $this->bootView( $view );
        }
    }

    /**
     * Print styles to the header.
     *
     * @return void
     */
    public function print_styles() { ?>
        <style>
            <?php
            printf(
                '.appearance_page_%1$s .wp-filter { margin-bottom: 15px; }',
                esc_html( $this->name )
            )
            ?>
        </style>
        <?php
    }

    /**
     * Outputs the settings page to the screen.
     *
     * @return void
     */
    public function template() {
        ?>

        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo esc_html( $this->label ); ?></h1>

            <div class="wp-filter">
                <?php $this->filterLinks(); ?>
            </div>

            <?php $this->currentView()->template(); ?>
        </div><!-- wrap -->
        <?php
    }

    /**
     * Displays the filter links (tabs) bar at the top of the page.
     *
     * @return void
     */
    protected function filterLinks() {
        ?>

        <ul class="filter-links">

            <?php
            foreach ( $this->views as $view ) :

                // Get the URL.
                $url = admin_url( 'options-general.php' );

                $url = add_query_arg( [
                    'page' => $this->name,
                    'view' => $view->name(),
                ], $url );

                if ( 'general' === $view->name() ) :
                    $url = remove_query_arg( 'view', $url );
                endif;
                ?>

                <li class="<?php echo sanitize_html_class( $view->name() ); ?>">
                    <?php
                    printf(
                        '<a href="%s"%s>%s</a>',
                        esc_url( $url ),
                        $view->name() === $this->currentView()->name() ? ' class="current"' : '',
                        esc_html( $view->label() )
                    )
                    ?>
                </li>

            <?php endforeach ?>

        </ul>
        <?php
    }

    /**
     * Adds a view.
     *
     * @param  string|object $view
     * @return void
     */
    public function addView( $view ) {

        if ( is_string( $view ) ) {
            $view = $this->resolveView( $view );
        }

        $this->views[ $view->name() ] = $view;
    }

    /**
     * Resolves a view in the case that it is a string and not an object.
     *
     * @param  string
     * @return \SzepeViktor\WordPress\Waf\Settings\Admin\Views\View
     */
    protected function resolveView( $view ) {
        return new $view( $this );
    }

    /**
     * Calls a view's `register()` method.
     *
     * @param \SzepeViktor\WordPress\Waf\Settings\Admin\Views\View $view
     * @return void
     */
    protected function registerView( $view ) {
        if ( method_exists( $view, 'register' ) ) {
            $view->register();
        }
    }

    /**
     * Calls a view's `boot()` method.
     *
     * @param \SzepeViktor\WordPress\Waf\Settings\Admin\Views\View $view
     * @return void
     */
    protected function bootView( $view ) {
        if ( method_exists( $view, 'boot' ) ) {
            $view->boot();
        }
    }

    /**
     * Returns the collection of views.
     *
     * @return \SzepeViktor\WordPress\Waf\Settings\Admin\Views\Views
     */
    protected function getViews() {
        return $this->views;
    }

    /**
     * Registers all views.
     *
     * @return void
     */
    protected function registerViews() {
        foreach ( $this->views as $view ) {
            $this->registerView( $view );
        }
    }

    /**
     * Boots all views.
     *
     * @return void
     */
    protected function bootViews() {
        foreach ( $this->views as $view ) {
            $this->bootView( $view );
        }
    }

    /**
     * Returns the current view object or `null`.
     *
     * @return null|\SzepeViktor\WordPress\Waf\Settings\Admin\Views\View
     */
    public function currentView() {
        $current = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'general';

        if ( isset( $this->views[ $current ] ) ) {
            return $this->views[ $current ];
        }

        return null;
    }

}

