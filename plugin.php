<?php
/**
 * Plugin Name: WAF for WordPress
 * Plugin URI: https://github.com/szepeviktor/waf4wordpress
 * Description: WAF for WordPress 🔥 with 60+ security checks and weekly updates.
 * Version: 0.0.2
 *
 * Author: Viktor Szépe
 * Author URI: https://szepe.net
 *
 * Text Domain: waf4wordpress
 *
 * Requires at least: 6.1.1
 * Requires PHP: 7.4
 *
 * @package    SzepeViktor\WordPress\Waf
 * @author     Viktor Szépe <viktor@szepe.net>
 * @copyright  Copyright (c) 2023, Viktor Szépe
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Don't execute code if file is accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'VS_WAF_PLUGIN_FILE', __FILE__ );
define( 'VS_WAF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VS_WAF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VS_WAF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once VS_WAF_PLUGIN_DIR . '/vendor/autoload.php';
require_once VS_WAF_PLUGIN_DIR . '/src/init.php';
