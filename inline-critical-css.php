<?php
/*
 * Plugin Name: Inline Critical CSS
 * Plugin URI: https://github.com/stezu/inline-critical-css
 * Description: Inline CSS critical to rendering above-the-fold content to improve perceived load time.
 * Version: 0.1.0
 * Author: Stephen Zuniga
 * Author URI: http://stephenzuniga.com
 *
 * License: GNU General Public License v2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

class ICCSS {
	public $head_stylesheets = NULL;

	public function __construct() {

		// Load javascript
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		// Move stylesheets to footer
		remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		add_action( 'wp_footer', 'wp_enqueue_scripts', 1 );
	}

	/**
	 * Load script that handles grabbing the critical styles
	 *
	 * @access		public
	 * @return		void
	 */
	public function load_scripts() {
		wp_enqueue_script( 'critical-css', 'assets/critical-css.js', array(), '0.1.0', true );
	}
}

// Start the plugin up
new ICCSS();
