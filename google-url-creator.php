<?php
/*
Plugin Name: Google URL Creator
Description: Generate custom URL strings that can be used to track campaigns in Google Analytics.
Version: 1.2
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

/**
 * Class GoogleURLCreator
 *
 * The main class for the plugin.
 *
 * @since 1.1
 */
class GoogleURLCreator {
	/**
	 * All of the terms input by the user.
	 *
	 * @since 1.1
	 */
	private $terms = array();

	/**
	 * The url generated from all of the terms.
	 *
	 * @since 1.1
	 */
	private $generated_url = '';

	/**
	 * The required notification.
	 *
	 * @since 1.1
	 */
	private $required = '<span style="color: #f00;">*</span>';

	/**
	 * If there's an error, this is true.
	 *
	 * @since 1.1
	 */
	private $error = false;

	/**
	 * Constructs the class.
	 *
	 * Get's the terms. Generates the URL with the supplied terms. Adds
	 * the admin menu to Tools.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		$this->get_terms();
		$this->generate_url();

		add_action( 'admin_menu', array( $this, 'create_submenu_page' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'button' ) );
	}

	/**
	 * Adds the page under Tools
	 *
	 * @since 1.1
	 */
	public function create_submenu_page() {
		add_submenu_page(
			'tools.php',
			'URL Creator',
			'URL Creator',
			'manage_options',
			'google-url-creator',
			array( $this, 'page_body' )
		);
	}

	/**
	 * Gets all of the terms.
	 *
	 * Takes all of the terms that the user input, runs them through
	 * sanitation, and then saves them.
	 *
	 * @since 1.1
	 */
	private function get_terms() {
		$bad  = array( " ", "'", '"' );
		$good = array( "%20", "", "%22" );

		//Unclean variables
		$pre_source  = str_replace( $bad, $good, $_POST['source'] );
		$pre_medium  = str_replace( $bad, $good, $_POST['medium'] );
		$pre_term    = str_replace( $bad, $good, $_POST['term'] );
		$pre_content = str_replace( $bad, $good, $_POST['content'] );
		$pre_name    = str_replace( $bad, $good, $_POST['name'] );

		//Clean variables
		$this->terms['site']        = get_site_url();
		$this->terms['source']      = stripslashes( $pre_source );
		$this->terms['medium']      = stripslashes( $pre_medium );
		$this->terms['term']        = stripslashes( $pre_term );
		$this->terms['content']     = stripslashes( $pre_content );
		$this->terms['name']        = stripslashes( $pre_name );
		$this->terms['select_page'] = $_POST['select-page'];
	}

	/**
	 * Generates the URL.
	 *
	 * @since 1.1
	 */
	private function generate_url() {
		$this->generated_url = $this->terms['select_page'] . '?';
		$this->generated_url .= 'utm_source=' . $this->terms['source'];
		$this->generated_url .= '&utm_medium=' . $this->terms['medium'];
		if ( ! empty( $this->terms['term'] ) ) {
			$this->generated_url .= '&utm_term=' . $this->terms['term'];
		}
		if ( ! empty( $this->terms['content'] ) ) {
			$this->generated_url .= '&utm_content=' . $this->terms['content'];
		}
		$this->generated_url .= '&utm_campaign=' . $this->terms['name'];
	}

	/**
	 * Checks to see if there's any errors.
	 *
	 * If any of the 3 required terms are missing, an error nag
	 * is output and the class is set to error=true.
	 *
	 * @since 1.1
	 */
	private function error_check() {
		if ( empty( $this->terms['source'] ) || empty( $this->terms['medium'] ) || empty( $this->terms['name'] ) ) {
			echo '<div class="message error">';
			echo '<p><strong>ERROR:</strong> Please supply the following:</p>';
			echo '<ul>';
			echo empty( $this->terms['source'] ) ? '<li>Campaign Source</li>' : '';
			echo empty( $this->terms['medium'] ) ? '<li>Campaign Medium</li>' : '';
			echo empty( $this->terms['name'] ) ? '<li>Campaign Name</li>' : '';
			echo '</ul>';
			echo '</div>';

			$this->error = true;
		}
	}

	/**
	 * Outputs the generated URL if no errors.
	 *
	 * @since 1.1
	 */
	private function output_url() {
		if ( $this->error ) {
			return;
		}

		echo '<code style="font-size: 2em;line-height: 1.5em;">' . $this->generated_url . '</code>';
	}

	/**
	 * The HTML output for the page.
	 *
	 * @since 1.1
	 */
	public function page_body() {
		$this->error_check();
		?>
		<div class="wrap">
			<h2>Generate a custom campaign URL</h2>

			<form method="post">
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="source">Campaign Source <?php echo $this->required; ?></label>
						</th>
						<td>
							<input type="text" name="source" id="source"
							       value="<?php if ( ! empty( $this->terms['source'] ) ) {
								       echo $this->terms['source'];
							       } ?>" class="txt requiredField"/>

							<p class="description">(referrer: google, citysearch, newsletter4)</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="source">Campaign Medium <?php echo $this->required; ?></label>
						</th>
						<td>
							<input type="text" name="medium" id="medium"
							       value="<?php if ( ! empty( $this->terms['medium'] ) ) {
								       echo $this->terms['medium'];
							       } ?>" class="txt requiredField"/>

							<p class="description">(marketing medium: cpc, banner, email)</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="source">Campaign Term</label>
						</th>
						<td>
							<input type="text" name="term" id="term"
							       value="<?php if ( ! empty( $this->terms['term'] ) ) {
								       echo $this->terms['term'];
							       } ?>" class="txt"/>

							<p class="description">(identify the paid keywords)</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="source">Campaign Content</label>
						</th>
						<td>
							<input type="text" name="content" id="content"
							       value="<?php if ( ! empty( $this->terms['content'] ) ) {
								       echo $this->terms['content'];
							       } ?>" class="txt"/>

							<p class="description">(use to differentiate ads)</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="source">Campaign Name <?php echo $this->required; ?></label>
						</th>
						<td>
							<input type="text" name="name" id="name"
							       value="<?php if ( ! empty( $this->terms['name'] ) ) {
								       echo $this->terms['name'];
							       } ?>" class="txt"/>

							<p class="description">(product, promo code or slogan)</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label>Choose your page:</label>
						</th>
						<td>
							<?php
							$the_pages = get_posts( array(
								'post_type'      => 'any',
								'post_status'    => 'publish',
								'orderby'        => 'title',
								'order'          => 'ASC',
								'posts_per_page' => - 1
							) );
							if ($the_pages) {
							?>
							<select name="select-page">
								<option value="">= No Page =</option>
								<option value="<?php echo $this->terms['site']; ?>/">= Home =</option>
								<?php
								foreach ( $the_pages as $page ) {
									$page_id = $page->ID;
									$slug    = get_permalink( $page_id );
									$title   = $page->post_title;
									?>
									<option value="<?php echo $slug; ?>"><?php echo $title; ?></option>
								<?php
								}
								echo "</select>";
								}
								?>
						</td>
					</tr>
					</tbody>
				</table>

				<p class="submit">
					<input class="button-primary button" type="submit" value="Generate" name="submit" id="submit"/>
				</p>
			</form>

			<?php $this->output_url(); ?>

			<div class="updated">
				<h3>Notes</h3>
				<ul>
					<li>None of this works right in <a href="http://google.com/analytics">Google Analytics</a> without
						Source, Medium and Name
					</li>
					<li>You can view the results of your campaign by going to Reporting- >Acquisition- >Campaigns in <a
							href="http://google.com/analytics">Google Analytics</a></li>
					<li>If you wish to build a URL for something other than what is available in the dropdown, simply
						select
						NO PAGE in the dropdown and append the results to the URL of your other content type
					</li>
					<li>A companion plugin to this which can be used to integrate the parameters in the URL you create
						with
						your site content is <a href="http://wordpress.org/plugins/google-campaign-text-replacer/">available
							here</a>.
					</li>
				</ul>
			</div>
			<p>
				For more information visit <a href="https://support.google.com/analytics/answer/1033867?hl=en">Google's
					URL builder page</a>.
			</p>
		</div>
	<?php
	}

	/**
	 * Append button to button group below title
	 *
	 * Since 1.2
	 */
	public function button() {

		$button = "<a href='#' class='button button-small'>Campaign URL</a>";

		echo '<script type="text/javascript">document.getElementById("edit-slug-box").innerHTML += "' . $button . '";</script>';
	}
}

$googleurlcreator = new GoogleURLCreator();