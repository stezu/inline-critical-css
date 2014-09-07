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

		// Move stylesheets to footer
		add_action( 'wp_head', array( $this, 'remove_head_stylesheets' ), 2 );
		add_action( 'wp_footer', array( $this, 'output_footer_stylesheets' ), 1 );

		// Load javascript
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	/**
	 * Load script that handles grabbing the critical styles
	 *
	 * @access		public
	 * @return		void
	 */
	public function load_scripts() {
		wp_enqueue_script( 'critical-css', plugins_url( '/assets/critical-css.js', __FILE__), array(), '0.1.0' );
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
}

// Start the plugin up
new ICCSS();
