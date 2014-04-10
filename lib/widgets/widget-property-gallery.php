<?php
/*
 * WIDGET :: Property Gallery
 */

class EPL_Widget_Property_Gallery extends WP_Widget {

	function __construct() {
		parent::WP_Widget(false, $name = 'EPL - Property Gallery');	
	}

	function widget($args, $instance) {
		extract( $args );
		$title 		= apply_filters('widget_title', $instance['title']);
		$d_columns	= $instance['d_columns'];
		$attachments = get_children( array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image') ); 
		if ( !empty( $attachments ) ) {
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;
			
			$gall = '[gallery columns="'. $d_columns . '" link="file"]';
			echo do_shortcode($gall);
			echo $after_widget;
		}
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['d_columns'] = strip_tags($new_instance['d_columns']);
		return $instance;
	}

	function form($instance) {
		$title 		= esc_attr($instance['title']);
		$d_columns	= esc_attr($instance['d_columns']); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('d_columns'); ?>"><?php _e('Number of Properties'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('d_columns'); ?>" name="<?php echo $this->get_field_name('d_columns'); ?>">
				<?php
					for ($i=1;$i<=6;$i++) {
						echo '<option value="'.$i.'"'; if ($i==$instance['d_columns']) echo ' selected="selected"'; echo '>'.$i.'</option>';
					}
				?>
			</select>
		</p>
		<?php 
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Property_Gallery");') );