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
	public $transient_location = NULL;
	public $head_stylesheets = NULL;
	public $link_items = NULL;
	public $cached_css = false;

	public function __construct() {

		$this->transient_location = 'ICCSS_cached_css';
		$this->cached_css = get_transient( $this->transient_location );

		// If the critical css is cached
		if ( $this->cached_css ) {

			// Move stylesheets to footer
			add_action( 'wp_head', array( $this, 'remove_head_stylesheets' ), 2 );
			add_action( 'wp_footer', array( $this, 'output_footer_stylesheets' ), 1 );

			// Add critical css to the head
			add_action( 'wp_head', array( $this, 'output_critical_css' ), 1 );

		} else {

			// Load javascript
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

			// Cache critical css with ajax call
			add_action( 'wp_ajax_iccss_cache_critical_css', array( $this, 'cache_critical_css' ) );
		}
	}

	/**
	 * Load script that handles grabbing the critical styles
	 *
	 * @access		public
	 * @return		void
	 */
	public function load_scripts() {
		wp_enqueue_script( 'critical-css', plugins_url( '/assets/critical-css.js', __FILE__), array(), '0.1.0' );
		wp_localize_script( 'critical-css', 'iccss', array(
			'ajaxurl'	=> admin_url('admin-ajax.php'),
		));
	}

	/**
	 * Remove stylesheets from the head
	 *
	 * @access		public
	 * @return		void
	 */
	public function remove_head_stylesheets() {
		global $wp_styles;

		$this->head_stylesheets = $wp_styles->queue;

		$wp_styles->dequeue( $this->head_stylesheets );
	}

	/**
	 * Output styles in the footer
	 *
	 * @access		public
	 * @return		void
	 */
	public function output_footer_stylesheets() {
		global $wp_styles;

		ob_start();
		$wp_styles->do_items( $this->head_stylesheets );
		$this->link_items = ob_get_clean();

		if ( $this->link_items ) {

			// Output stylesheets in a non-blocking manner
			?>
			<script type="text/javascript">
				var links = <?php echo json_encode( $this->link_items ); ?>,
					raf = requestAnimationFrame || mozRequestAnimationFrame ||
						  webkitRequestAnimationFrame || msRequestAnimationFrame,
					cb = function () {
						document.getElementsByTagName("head")[0].insertAdjacentHTML('beforeend', links);
					};

				if (raf) {
					raf(cb);
				} else {
					window.addEventListener('load', cb);
				}
			</script>
			<?php

			// Output stylesheets in a noscript tag for fallback
			echo '<noscript>' . $this->link_items . '</noscript>';
		}
	}

	/**
	 * Cache the returned critical css for the page
	 *
	 * @access		public
	 * @return		void
	 */
	public function cache_critical_css() {
		$critical_css = $_REQUEST['critical_css'];
		$expires = 604800;

		set_transient( $this->transient_location, $critical_css, $expires );
		die();
	}

	/**
	 * Output the cached css in a style tag
	 *
	 * @access		public
	 * @return		void
	 */
	public function output_critical_css() {
		echo '<style type="text/css">' . $this->cached_css . '</style>';
	}
}

// Start the plugin up
new ICCSS();
