<?php
/*
Plugin Name: Mio Custom Recent Posts Widget
Author: Miosee
Plugin URI: https://webs-k.com/wp/mio-custom-recent-posts
Description: Widget: Custom Recent Posts
Version: 1.0.0
Author URI: https://webs-k.com/
Text Domain: mio-custom-recent-posts
Domain Path: /languages
*/

/**
 * Custom Recent Posts Widget
 *
 * @since 1.0.0
 * @author miosee
 */
add_action(
	'widgets_init',
	create_function('', 'return register_widget("Mio_Custom_Recent_Posts");')
);

class Mio_Custom_Recent_Posts extends WP_Widget {
	/**
	 * Widget Register
	 */
	public function __construct()
	{
		if ( strpos(plugin_basename( __FILE__ ), '/wp-content/themes/' ) == false ) {
			load_plugin_textdomain(
				'mio-custom-recent-posts',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		} else {
			load_textdomain(
				'mio-custom-recent-posts',
				get_template_directory() . '/mio-custom-recent-posts/languages/mio-custom-recent-posts-'.get_locale().'.mo'
			);
		}

		add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));

		$widget_ops = array(
			'classname' => 'widget_custom_recent_entries',
			'description' => __('That can be configured to some extent New Posts', 'mio-custom-recent-posts')
		);
		$control_ops = array(
			'width' => 400, 'height' => 350
		);
		parent::__construct(
			false,
			__('Custom Recent Posts', 'mio-custom-recent-posts'),
			$widget_ops
		);
		$this->alt_option_name = 'widget_custom_recent_entries';
	}

	public function wp_enqueue_scripts() {
		wp_enqueue_style('mwcrp', plugins_url('mio-custom-recent-posts.css', __FILE__ ), array(), '1.0.0' );
	}

	/**
	 * Widget Output
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_custom_recent_posts', 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id']) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __('Recent Posts', 'mio-custom-recent-posts');

		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$media_size = empty( $instance['media_size'] ) ? 'thumbnail' : $instance['media_size'];

		$show_thumbnail = isset( $instance['show_thumbnail'] ) ? $instance['show_thumbnail'] : false;

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;

		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		$post_type = empty( $instance['post_type'] ) ? 'post' : $instance['post_type'];

		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'post_type'           => $post_type,
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true
		) ) );

		if ($r->have_posts()) :
?>
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<a href="<?php the_permalink(); ?>">
				<?php if( $show_thumbnail ) : ?>
					<span class="thumb">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( $media_size ); ?>
					<?php else : ?>
						<?php
							$child_args = array(
								'post_parent' => get_the_ID(),
								'post_type' => 'attachment',
								'post_mime' => 'image',
							);
							$files = get_children( $child_args );
							if ( ! empty($files) ) :
								$keys = array_keys( $files );
								$attachment_id = $keys[0];
								$get_image = wp_get_attachment_image_src( $attachment_id, $media_size );
?>
							<img src="<?php echo esc_url($get_image[0]); ?>" width="<?php echo esc_attr($get_image[1]); ?>" height="<?php echo esc_attr($get_image[2]); ?>" alt="">
						<?php else : ?>
							<span class="not-thumb"></span>
						<?php endif; ?>
					<?php endif; ?>
					</span>
				<?php endif; ?>
					<span class="title"><?php get_the_title() ? the_title() : the_ID(); ?></span>
				</a>
				<?php if ( $show_date ) : ?>
					<?php $on_thumb = $show_thumbnail ? ' on-thumb' : ''; ?>
					<span class="post-date<?php echo $on_thumb; ?>"><?php echo get_the_date(); ?></span>
				<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $args['after_widget']; ?>
<?php
		wp_reset_postdata();

		endif;

		if( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_custom_recent_posts', $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	}

	/**
	 * Admin Options Form Update
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if( in_array( $new_instance['media_size'], array( 'thumbnail', 'medium', 'large', 'full' ) ) ) {
			$instance['media_size'] = $new_instance['media_size'];
		} else {
			$instance['media_size'] = 'thumbnail';
		}
		$instance['show_thumbnail'] = isset( $new_instance['show_thumbnail'] ) ? (bool) $new_instance['show_thumbnail'] : false;
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;

		$post_types_args = array(
			'public' => true,
		);
		$post_types = get_post_types($post_types_args);
		$post_type_name = array();
		foreach ( $post_types as $name ) {
			array_push( $post_type_name, $name );
		}
		if ( in_array( $new_instance['post_type'], $post_type_name ) ) {
			$instance['post_type'] = $new_instance['post_type'];
		} else {
			$instance['post_type'] = 'post';
		}


		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if( isset($alloptions['widget_custom_recent_entries']) )
			delete_option('widget_custom_recent_entries');

		return $instance;
	}

	/**
	 * @access public
	 */
	public function flush_widget_cache()
	{
		wp_cache_delete('widget_custom_recent_posts', 'widget');
	}

	/**
	 * Widget inline Forms
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance
	 */
	public function form( $instance )
	{
		// global $_wp_additional_image_sizes;
		// var_dump($_wp_additional_image_sizes);
		$instance = wp_parse_args( (array) $instance, array( 'media_size' => 'thumbnail', 'title' => '', 'show_thumbnail' => false, 'number' => 5, 'show_date' => false, 'post_type' => 'post' ) );
		$title = esc_attr( $instance['title'] );
		$show_thumbnail = $instance['show_thumbnail'];
		$number = absint($instance['number']);
		$show_date = $instance['show_date'];

		$post_types_args = array(
			'public' => true,
		);
		$post_types = get_post_types($post_types_args, 'object');

		// Attachment Size
		$attachment_size = array(
			'thumbnail' => array(
				'width' => intval(get_option('thumbnail_size_w')),
				'height' => intval(get_option('thumbnail_size_h'))
			),
			'medium' => array(
				'width' => intval(get_option('medium_size_w')),
				'height' => intval(get_option('medium_size_h'))
			),
			'large' => array(
				'width' => intval(get_option('large_size_w')),
				'height' => intval(get_option('large_size_h'))
			),
		);
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mio-custom-recent-posts' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><input class="checkbox" type="checkbox" <?php checked( $show_thumbnail ); ?> id="<?php echo $this->get_field_id('show_thumbnail'); ?>" name="<?php echo $this->get_field_name('show_thumbnail'); ?>" />
		<label for="<?php echo $this->get_field_id('show_thumbnail'); ?>"><?php _e( 'Show Thumbnail', 'mio-custom-recent-posts' ); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('media_size'); ?>"><?php _e( 'Thumbnail Size:', 'mio-custom-recent-posts'); ?></label>
		<select name="<?php echo $this->get_field_name('media_size'); ?>" id="<?php $this->get_field_id('media_size'); ?>" class="widefat">
			<option value="thumbnail"<?php selected( $instance['media_size'], 'thumbnail' ); ?>><?php _e( 'Thumbnail', 'mio-custom-recent-posts' ); ?> - <?php echo $attachment_size['thumbnail']['width']; ?> x <?php echo $attachment_size['thumbnail']['height']; ?></option>
			<option value="medium"<?php selected( $instance['media_size'], 'medium'); ?>><?php _e( 'Medium', 'mio-custom-recent-posts' ); ?> - <?php echo $attachment_size['medium']['width']; ?> x <?php echo $attachment_size['medium']['height']; ?></option>
			<option value="large"<?php selected( $instance['media_size'], 'large'); ?>><?php _e( 'Large', 'mio-custom-recent-posts' ); ?> - <?php echo $attachment_size['large']['width']; ?> x <?php echo $attachment_size['large']['height']; ?></option>
			<option value="full"<?php selected( $instance['media_size'], 'full'); ?>><?php _e( 'Full', 'mio-custom-recent-posts' ); ?></option>
		</select></p>
		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of posts to show:', 'mio-custom-recent-posts' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?', 'mio-custom-recent-posts' ); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e( 'Post Type:', 'mio-custom-recent-posts'); ?></label>
		<select name="<?php echo $this->get_field_name('post_type'); ?>" id="<?php echo $this->get_field_id('post_type'); ?>" class="widefat">
		<?php foreach ( $post_types as $key => $obj ) : ?>
			<option value="<?php echo $key; ?>"<?php selected( $instance['post_type'], $key ); ?>><?php echo $obj->labels->name; ?></option>
		<?php endforeach; ?>
		</select>
		</p>
<?php
	}
}
