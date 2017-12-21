<?php
/**
 * Widget class.
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2009-2017 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Create a Wordpress Widget for CRP.
 *
 * @since 1.9
 *
 * @extends WP_Widget
 */
class CRP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'widget_crp',
			__( 'Related Posts [CRP]', 'contextual-related-posts' ),
			array(
				'description' => __( 'Display Related Posts', 'contextual-related-posts' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Back-end widget form.
	 *
	 * @see	WP_Widget::form()
	 *
	 * @param	array $instance   Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$limit = isset( $instance['limit'] ) ? esc_attr( $instance['limit'] ) : '';
		$offset = isset( $instance['offset'] ) ? esc_attr( $instance['offset'] ) : '';
		$show_excerpt = isset( $instance['show_excerpt'] ) ? esc_attr( $instance['show_excerpt'] ) : '';
		$show_author = isset( $instance['show_author'] ) ? esc_attr( $instance['show_author'] ) : '';
		$show_date = isset( $instance['show_date'] ) ? esc_attr( $instance['show_date'] ) : '';
		$post_thumb_op = isset( $instance['post_thumb_op'] ) ? esc_attr( $instance['post_thumb_op'] ) : '';
		$thumb_height = isset( $instance['thumb_height'] ) ? esc_attr( $instance['thumb_height'] ) : '';
		$thumb_width = isset( $instance['thumb_width'] ) ? esc_attr( $instance['thumb_width'] ) : '';

		// Parse the Post types.
		$post_types = array();

		// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
		if ( ! empty( $instance['post_types'] ) && false === strpos( $instance['post_types'], '=' ) ) {
			$post_types = explode( ',', $instance['post_types'] );
		} else {
			parse_str( $instance['post_types'], $post_types );	// Save post types in $post_types variable.
		}

		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		$posts_types_inc = array_intersect( $wp_post_types, $post_types );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
			<?php esc_html_e( 'Title', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>">
			<?php esc_html_e( 'No. of posts', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>">
			<?php esc_html_e( 'Offset', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="text" value="<?php echo esc_attr( $offset ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>" type="checkbox" <?php if ( $show_excerpt ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( ' Show excerpt?', 'contextual-related-posts' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_author' ) ); ?>" type="checkbox" <?php if ( $show_author ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( ' Show author?', 'contextual-related-posts' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" type="checkbox" <?php if ( $show_date ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( ' Show date?', 'contextual-related-posts' ); ?>
			</label>
		</p>
		<p>
			<?php esc_html_e( 'Thumbnail options', 'contextual-related-posts' ); ?>: <br />
			<select class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'post_thumb_op' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'post_thumb_op' ) ); ?>">
			  <option value="inline" <?php selected( 'inline', $post_thumb_op, true ); ?>><?php esc_html_e( 'Thumbnails inline, before title','contextual-related-posts' ); ?></option>
			  <option value="after" <?php selected( 'after', $post_thumb_op, true ); ?>><?php esc_html_e( 'Thumbnails inline, after title','contextual-related-posts' ); ?></option>
			  <option value="thumbs_only" <?php selected( 'thumbs_only', $post_thumb_op, true ); ?>><?php esc_html_e( 'Only thumbnails, no text','contextual-related-posts' ); ?></option>
			  <option value="text_only" <?php selected( 'text_only', $post_thumb_op, true ); ?>><?php esc_html_e( 'No thumbnails, only text.','contextual-related-posts' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumb_height' ) ); ?>">
			<?php esc_html_e( 'Thumbnail height', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'thumb_height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumb_height' ) ); ?>" type="text" value="<?php echo esc_attr( $thumb_height ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumb_width' ) ); ?>">
			<?php esc_html_e( 'Thumbnail width', 'contextual-related-posts' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'thumb_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumb_width' ) ); ?>" type="text" value="<?php echo esc_attr( $thumb_width ); ?>" />
			</label>
		</p>

		<p><?php esc_html_e( 'Post types to include', 'contextual-related-posts' ); ?>:<br />

			<?php foreach ( $wp_post_types as $wp_post_type ) { ?>

				<label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'post_types' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_types' ) ); ?>[]" type="checkbox" value="<?php echo esc_attr( $wp_post_type ); ?>" <?php checked( true, in_array( $wp_post_type, $posts_types_inc, true ) ); ?> />
					<?php echo esc_attr( $wp_post_type ); ?>
				</label>
				<br />

			<?php }	?>
		</p>

		<?php
			/**
			 * Fires after Contextual Related Posts widget options.
			 *
			 * @since 2.1.0
			 *
			 * @param	array	$instance	Widget options array
			 */
			do_action( 'crp_widget_options_after', $instance );
		?>

		<?php
	} //ending form creation

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param 	array $new_instance Values just sent to be saved.
	 * @param 	array $old_instance Previously saved values from database.
	 *
	 * @return 	array	Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = $new_instance['limit'];
		$instance['offset'] = $new_instance['offset'];
		$instance['show_excerpt'] = $new_instance['show_excerpt'];
		$instance['show_author'] = $new_instance['show_author'];
		$instance['show_date'] = $new_instance['show_date'];
		$instance['post_thumb_op'] = $new_instance['post_thumb_op'];
		$instance['thumb_height'] = $new_instance['thumb_height'];
		$instance['thumb_width'] = $new_instance['thumb_width'];

		// Process post types to be selected.
		$wp_post_types	= get_post_types( array(
			'public'	=> true,
		) );
		$post_types = isset( $new_instance['post_types'] ) ? $new_instance['post_types'] : array();
		$post_types = array_intersect( $wp_post_types, $post_types );
		$instance['post_types'] = implode( ',', $post_types );

		delete_post_meta_by_key( 'crp_related_posts_widget' ); // Delete the cache.

		/**
		 * Filters Update widget options array.
		 *
		 * @since 2.0.0
		 *
		 * @param array $instance Widget options array
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 */
		return apply_filters( 'crp_widget_options_update', $instance, $new_instance, $old_instance );
	} //ending update

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param	array $args   Widget arguments.
	 * @param	array $instance   Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $post, $crp_settings;

		// Get the post meta.
		if ( isset( $post ) ) {
			$crp_post_meta = get_post_meta( $post->ID, 'crp_post_meta', true );

			if ( isset( $crp_post_meta['disable_here'] ) && $crp_post_meta['disable_here'] ) {
				return;
			}
		}

		// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
		if ( ! empty( $crp_settings['exclude_on_post_types'] ) && false === strpos( $crp_settings['exclude_on_post_types'], '=' ) ) {
			$exclude_on_post_types = explode( ',', $crp_settings['exclude_on_post_types'] );
		} else {
			parse_str( $crp_settings['exclude_on_post_types'], $exclude_on_post_types );	// Save post types in $exclude_on_post_types variable.
		}

		if ( is_object( $post ) && ( in_array( $post->post_type, $exclude_on_post_types, true ) ) ) {
			return 0;	// Exit without adding related posts.
                }

		$exclude_on_post_ids = explode( ',', $crp_settings['exclude_on_post_ids'] );

		if ( ( ( is_single() ) && ( ! is_single( $exclude_on_post_ids ) ) ) || ( ( is_page() ) && ( ! is_page( $exclude_on_post_ids ) ) ) ) {

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? strip_tags( str_replace( '%postname%', $post->post_title, $crp_settings['title'] ) ) : $instance['title'] );

			$limit = isset( $instance['limit'] ) ? $instance['limit'] : $crp_settings['limit'];
			if ( empty( $limit ) ) {
				$limit = $crp_settings['limit'];
			}
			$offset = isset( $instance['offset'] ) ? $instance['offset'] : 0;

			$post_thumb_op = isset( $instance['post_thumb_op'] ) ? esc_attr( $instance['post_thumb_op'] ) : 'text_only';
			$thumb_height = isset( $instance['thumb_height'] ) ? esc_attr( $instance['thumb_height'] ) : $crp_settings['thumb_height'];
			$thumb_width = isset( $instance['thumb_width'] ) ? esc_attr( $instance['thumb_width'] ) : $crp_settings['thumb_width'];
			$show_excerpt = isset( $instance['show_excerpt'] ) ? esc_attr( $instance['show_excerpt'] ) : '';
			$show_author = isset( $instance['show_author'] ) ? esc_attr( $instance['show_author'] ) : '';
			$show_date = isset( $instance['show_date'] ) ? esc_attr( $instance['show_date'] ) : '';
			$post_types = isset( $instance['post_types'] ) && ! empty( $instance['post_types'] ) ? $instance['post_types'] : $crp_settings['post_types'];

			$arguments = array(
				'is_widget' => 1,
				'limit' => $limit,
				'offset' => $offset,
				'show_excerpt' => $show_excerpt,
				'show_author' => $show_author,
				'show_date' => $show_date,
				'post_thumb_op' => $post_thumb_op,
				'thumb_height' => $thumb_height,
				'thumb_width' => $thumb_width,
				'post_types' => $post_types,
			);

			/**
			 * Filters arguments passed to crp_pop_posts for the widget.
			 *
			 * @since 2.0.0
			 *
			 * @param array $arguments Widget options array.
			 * @param array $args Widget arguments.
			 * @param array $instance Saved values from database.
			 */
			$arguments = apply_filters( 'crp_widget_options' , $arguments, $args, $instance );

/*********************************************************************************************************/
/*************************************BEGIN modifications EPA***************************************/
/*********************************************************************************************************/
$arguments = apply_filters('crp_widget_options', $arguments, $args, $instance);
$output = $args['before_widget'];
$output.= $args['before_title'] . $title . $args['after_title'];

// Obtain IP Address

if (!empty($_SERVER['HTTP_CLIENT_IP']))
{

	// check ip from share internet

	$ip = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
{

	// to check ip is pass from proxy

	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
	$ip = $_SERVER['REMOTE_ADDR'];
}

// Query the database for this session's IP address to obtain it's browsing history

global $wpdb;
$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM wp_wassup WHERE ip = '$ip'");

// If there are less than 10 entries, output RELATED POSTS

if ($rowcount <= 10)
{
	$output.= get_crp($arguments);

	// Otherwise, we begin RECOMMENDATIONS

}
else
{
	$output.= '<ul>';

	// Store all url strings into an array

	$requestedurls = $wpdb->get_results("SELECT urlrequested FROM wp_wassup WHERE ip = '$ip' ORDER BY timestamp DESC LIMIT 50");
	$post_names = array();

	// Begin conversion process from url string to post name

	foreach($requestedurls as $requestedurl)
	{

		// Strip out categories as we do not want those taxonomy objects

		if (strpos($requestedurl->urlrequested, 'category') == false)
		{
			$urlarray = explode("/", $requestedurl->urlrequested);
			$end = $urlarray[count($urlarray) - 2];
			array_push($post_names, $end);
		}
	}

	// Create an array for post ID and begin conversion process from post name to post ID

	$post_id_arr = array();
	foreach($post_names as $post_name)
	{
		$get_id = $wpdb->get_var("SELECT ID FROM wp_posts WHERE post_name = '$post_name' AND post_name <> ''");
		array_push($post_id_arr, $get_id);
	}

	// Find most popular post ID's

	$num_post_visits = array_count_values($post_id_arr);
	arsort($num_post_visits);
	$popular = array_slice(array_keys($num_post_visits) , 0, 5, true);

	// Create an array for taxonomy ID's and begin conversion process of most popular post IDs to taxonomy ID's

	$tax_id_arr = array();
	foreach($popular as $popular_id)
	{
		$get_tax_id = $wpdb->get_results("SELECT term_taxonomy_id FROM wp_term_relationships WHERE object_id = '$popular_id'");
		foreach($get_tax_id as $tax_id)
		{
			array_push($tax_id_arr, $tax_id->term_taxonomy_id);
		}

		// Sanitize taxonomy ID's and remove duplicates

		$clean_tax_array = array_unique($tax_id_arr);
	}

	// Convert array into comma separated string to be stored in database

	$tax_str = implode(",", $clean_tax_array);

	// Check to see if IP exists in our wp_recommendations table

	$ip_check = $wpdb->get_var($wpdb->prepare("SELECT IP FROM " . $wpdb->prefix . "recommendations
                                                                           WHERE IP = %s LIMIT 1", $ip));

	// If this IP already exists, then we check to see when it was last updated

	if (!empty($ip_check))
	{
		$date_check = $wpdb->get_var($wpdb->prepare("SELECT Timestamp FROM " . $wpdb->prefix . "recommendations
                                                                            WHERE IP = %s LIMIT 1", $ip));
		$time = strtotime($date_check);
		$one_week_ago = strtotime('-1 week');
		if ($time > $one_week_ago)
		{
			$wpdb->update($wpdb->prefix . 'recommendations', array(
				'Terms' => $tax_str,
				'Timestamp' => current_time('mysql', 1)
			) , array(
				"IP" => $ip
			));
		}

		// If this IP does not yet exist, we INSERT it into wp_recommendations

	}
	else
	{
		$wpdb->insert($wpdb->prefix . 'recommendations', array(
			'IP' => $ip,
			'Terms' => $tax_str,
			'Timestamp' => current_time('mysql', 1)
		));
	}

	// Query database and convert comma separated list of taxonomy ID's to taxonomy terms and links

	$term_lookup = $wpdb->get_var($wpdb->prepare("SELECT Terms FROM " . $wpdb->prefix . "recommendations
                                                                            WHERE IP = %s LIMIT 1", $ip));

	// Convert term ID to slug

	$terms = explode(",", $term_lookup);
    // Made an edit here - Steven 12/21/17 11:27AM
	foreach($terms as $term)
	{
		$get_term_name = $wpdb->get_var("SELECT name FROM wp_terms WHERE term_id = $term");
        if($get_term_name != 'admin')
        {
		    $get_term_link = get_term_link((int)$term);
            $output.= '<li class="cat-item cat-item-2"><a href=" ' . $get_term_link . ' ">' . $get_term_name . '</a></li>';
        }
	}
    // End Steven Edit

	$output.= '</ul>';
}

/*********************************************************************************************************/
/******************************************END modifications EPA******************************************/
/*********************************************************************************************************/

			$output .= $args['after_widget'];

			echo $output; // WPCS: XSS OK.
		}// End if().
	} // Ending function widget.
}


/**
 * Initialise the widget.
 *
 * @since 1.9.1
 */
function register_crp_widget() {
	register_widget( 'CRP_Widget' );
}
add_action( 'widgets_init', 'register_crp_widget' );

									