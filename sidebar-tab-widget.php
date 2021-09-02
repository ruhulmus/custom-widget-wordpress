<?php

function hstngr_register_widget() {

register_widget( 'hstngr_widget' );
register_widget( 'custom_sidebar_widget' );


}

add_action( 'widgets_init', 'hstngr_register_widget' );

class hstngr_widget extends WP_Widget {

function __construct() {

parent::__construct(

// widget ID

'hstngr_widget',

// widget name

__('Package Tabs', ' hstngr_widget_domain'),

// widget description

array( 'description' => __( 'Additional Tab for Package details Page', 'hstngr_widget_domain' ), )

);

}

public function widget( $args, $instance ) {

$title = apply_filters( 'widget_title', $instance['title'] );

echo $args['before_widget'];

//if title is present

if ( ! empty( $title ) )

echo $args['before_title'] . $title . $args['after_title'];

//output

//echo __( 'test', 'hstngr_widget_domain' );
?>

 
<div class="custom_tab_content">
	 
 
<div class="tab">
  <button class="tablinks active" onclick="openCity(event, 'overview')">Overview</button>
  <!--button class="tablinks" onclick="openCity(event, 'location')">Location</button-->
  <button class="tablinks" onclick="openCity(event, 'include')">Optional</button>
  <button class="tablinks" onclick="openCity(event, 'other')">Important Information</button>

</div>

<div id="overview" class="tabcontent first_tab_detials">
   
  <?php 
		if (pdf_attachment_file(1," Package details") || pdf_attachment_file(2," Additional Information")){
		echo "<h3 class='social-share-title'> Download </h3>";
		echo pdf_attachment_file(1," Package details");
		echo '<br/>';
		echo pdf_attachment_file(2," Additional Information");
		echo '<br/>';	
		}

		
		
	//if ( have_posts() ) : while ( have_posts() ) : the_post();
		echo get_the_content();
	// endwhile;
	// endif;
 
		 

	 ?>
</div>

<!--div id="location" class="tabcontent">
   <?php 
	//if ( have_posts() ) : while ( have_posts() ) : the_post();
		 $content3 = get_post_meta(get_the_ID(), 'wo_location_details' , true );
		$content3 = htmlspecialchars_decode($content3);
		$content3 = wpautop( $content3 );
		echo $content3;
	 //endwhile;
	 //endif;
	 ?>	
</div-->

<div id="include" class="tabcontent">
   
	  <?php 
	//if ( have_posts() ) : while ( have_posts() ) : the_post();
		$content = get_post_meta(get_the_ID(), 'wo_inclusion_box' , true );
		$content = htmlspecialchars_decode($content);
		$content = wpautop( $content );
		echo $content;
	 //endwhile;
	 //endif;
	 ?>
</div>
<div id="other" class="tabcontent">

  <?php 
	//if ( have_posts() ) : while ( have_posts() ) : the_post();
		 $content2 = get_post_meta(get_the_ID(), 'wo_others_details' , true );
		$content2 = htmlspecialchars_decode($content2);
		$content2 = wpautop( $content2 );
		echo $content2;
	 //endwhile;
	 //endif;
	 ?>	
</div>

<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");

  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";


}
</script>
 </div>

<?php

echo $args['after_widget'];

}

public function form( $instance ) {

if ( isset( $instance[ 'title' ] ) )

$title = $instance[ 'title' ];

else

$title = __( 'Tabs Title', 'hstngr_widget_domain' );

?>

<p>

<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>

<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

</p>

<?php

}

public function update( $new_instance, $old_instance ) {

$instance = array();

$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

return $instance;

}

}

/*  Custom Widget for Bokun Payment*/
class custom_sidebar_widget extends WP_Widget {

	function __construct() {

	parent::__construct(

	// widget ID

	'custom_sidebar_widget',

	// widget name

	__('Bokun Product Widget (Custom)', 'custom_sidebar_widget_domain'),

	// widget description

	array( 'description' => __( 'Custom Widget for Bokun Integration', 'custom_sidebar_widget_domain' ), )

	);



	wp_add_inline_script( 'custom-html-widgets', sprintf( 'wp.customHtmlWidgets.idBases.push( %s );', wp_json_encode( $this->id_base ) ) );

		// Note that the widgets component in the customizer will also do
		// the 'admin_print_scripts-widgets.php' action in WP_Customize_Widgets::print_scripts().


		add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_admin_scripts' ) );

		// Note that the widgets component in the customizer will also do
		// the 'admin_footer-widgets.php' action in WP_Customize_Widgets::print_footer_scripts().
		add_action( 'admin_footer-widgets.php', array( 'WP_Widget_Custom_HTML', 'render_control_template_scripts' ) );

		// Note this action is used to ensure the help text is added to the end.
		add_action( 'admin_head-widgets.php', array( 'WP_Widget_Custom_HTML', 'add_help_text' ) );

	}

	public function widget( $args, $instance ) {
		global $post;

		// Override global $post so filters (and shortcodes) apply in a consistent context.
		$original_post = $post;
		if ( is_singular() ) {
			// Make sure post is always the queried object on singular queries (not from another sub-query that failed to clean up the global $post).
			$post = get_queried_object();
		} else {
			// Nullify the $post global during widget rendering to prevent shortcodes from running with the unexpected context on archive queries.
			$post = null;
		}

		// Prevent dumping out all attachments from the media library.
		add_filter( 'shortcode_atts_gallery', array( $this, '_filter_gallery_shortcode_attrs' ) );

		$instance = array_merge( $this->default_instance, $instance );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		// Prepare instance data that looks like a normal Text widget.
		$simulated_text_widget_instance = array_merge(
			$instance,
			array(
				'text'   => isset( $instance['content'] ) ? $instance['content'] : '',
				'filter' => false, // Because wpautop is not applied.
				'visual' => false, // Because it wasn't created in TinyMCE.
			)
		);
		unset( $simulated_text_widget_instance['content'] ); // Was moved to 'text' prop.

		/** This filter is documented in wp-includes/widgets/class-wp-widget-text.php */
		$content = apply_filters( 'widget_text', $instance['content'], $simulated_text_widget_instance, $this );

		// Adds 'noopener' relationship, without duplicating values, to all HTML A elements that have a target.
		$content = wp_targeted_link_rel( $content );

		/**
		 * Filters the content of the Custom HTML widget.
		 *
		 * @since 4.8.1
		 *
		 * @param string                $content  The widget content.
		 * @param array                 $instance Array of settings for the current widget.
		 * @param WP_Widget_Custom_HTML $widget   Current Custom HTML widget instance.
		 */
		$content = apply_filters( 'widget_custom_html_content', $content, $instance, $this );

		// Restore post global.
		$post = $original_post;
		remove_filter( 'shortcode_atts_gallery', array( $this, '_filter_gallery_shortcode_attrs' ) );

		// Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
		$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_text ', $args['before_widget'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div class="textwidget custom-html-widget">'; // The textwidget class is for theme styling compatibility.
		echo $content;
		echo '</div>';
		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Custom HTML widget instance.
	 *
	 * @since 4.8.1
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array_merge( $this->default_instance, $old_instance );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['content'] = $new_instance['content'];
		} else {
			$instance['content'] = wp_kses_post( $new_instance['content'] );
		}
		return $instance;
	}

	/**
	 * Loads the required scripts and styles for the widget control.
	 *
	 * @since 4.9.0
	 */
	public function enqueue_admin_scripts() {
		$settings = wp_enqueue_code_editor(
			array(
				'type'       => 'text/html',
				'codemirror' => array(
					'indentUnit' => 2,
					'tabSize'    => 2,
				),
			)
		);

		wp_enqueue_script( 'custom-html-widgets' );
		if ( empty( $settings ) ) {
			$settings = array(
				'disabled' => true,
			);
		}
		wp_add_inline_script( 'custom-html-widgets', sprintf( 'wp.customHtmlWidgets.init( %s );', wp_json_encode( $settings ) ), 'after' );

		$l10n = array(
			'errorNotice' => array(
				/* translators: %d: Error count. */
				'singular' => _n( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 1 ),
				/* translators: %d: Error count. */
				'plural'   => _n( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 2 ),
				// @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
			),
		);
		wp_add_inline_script( 'custom-html-widgets', sprintf( 'jQuery.extend( wp.customHtmlWidgets.l10n, %s );', wp_json_encode( $l10n ) ), 'after' );
	}

	/**
	 * Outputs the Custom HTML widget settings form.
	 *
	 * @since 4.8.1
	 * @since 4.9.0 The form contains only hidden sync inputs. For the control UI, see `WP_Widget_Custom_HTML::render_control_template_scripts()`.
	 *
	 * @see WP_Widget_Custom_HTML::render_control_template_scripts()
	 *
	 * @param array $instance Current instance.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default_instance );
		echo bloginfo('wpurl').'/wp-includes/widget/';
		echo get_template_directory();
		?>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="title sync-input" type="hidden" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		<textarea id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" class="content sync-input" hidden><?php echo esc_textarea( $instance['content'] ); ?></textarea>
		<?php
	}

	/**
	 * Render form template scripts.
	 *
	 * @since 4.9.0
	 */
	public static function render_control_template_scripts() {
		?>
		<script type="text/html" id="tmpl-widget-custom-html-control-fields">
			<# var elementIdPrefix = 'el' + String( Math.random() ).replace( /\D/g, '' ) + '_' #>
			<p>
				<label for="{{ elementIdPrefix }}title"><?php esc_html_e( 'Title:' ); ?></label>
				<input id="{{ elementIdPrefix }}title" type="text" class="widefat title">
			</p>

			<p>
				<label for="{{ elementIdPrefix }}content" id="{{ elementIdPrefix }}content-label"><?php esc_html_e( 'Content:' ); ?></label>
				<textarea id="{{ elementIdPrefix }}content" class="widefat code content" rows="16" cols="20"></textarea>
			</p>

			<?php if ( ! current_user_can( 'unfiltered_html' ) ) : ?>
				<?php
				$probably_unsafe_html = array( 'script', 'iframe', 'form', 'input', 'style' );
				$allowed_html         = wp_kses_allowed_html( 'post' );
				$disallowed_html      = array_diff( $probably_unsafe_html, array_keys( $allowed_html ) );
				?>
				<?php if ( ! empty( $disallowed_html ) ) : ?>
					<# if ( data.codeEditorDisabled ) { #>
						<p>
							<?php _e( 'Some HTML tags are not permitted, including:' ); ?>
							<code><?php echo implode( '</code>, <code>', $disallowed_html ); ?></code>
						</p>
					<# } #>
				<?php endif; ?>
			<?php endif; ?>

			<div class="code-editor-error-container"></div>
		</script>
		<?php
	}

	/**
	 * Add help text to widgets admin screen.
	 *
	 * @since 4.9.0
	 */
	public static function add_help_text() {
		$screen = get_current_screen();

		$content  = '<p>';
		$content .= __( 'Use the Custom HTML widget to add arbitrary HTML code to your widget areas.' );
		$content .= '</p>';

		if ( 'false' !== wp_get_current_user()->syntax_highlighting ) {
			$content .= '<p>';
			$content .= sprintf(
				/* translators: 1: Link to user profile, 2: Additional link attributes, 3: Accessibility text. */
				__( 'The edit field automatically highlights code syntax. You can disable this in your <a href="%1$s" %2$s>user profile%3$s</a> to work in plain text mode.' ),
				esc_url( get_edit_profile_url() ),
				'class="external-link" target="_blank"',
				sprintf(
					'<span class="screen-reader-text"> %s</span>',
					/* translators: Accessibility text. */
					__( '(opens in a new tab)' )
				)
			);
			$content .= '</p>';

			$content .= '<p id="editor-keyboard-trap-help-1">' . __( 'When using a keyboard to navigate:' ) . '</p>';
			$content .= '<ul>';
			$content .= '<li id="editor-keyboard-trap-help-2">' . __( 'In the editing area, the Tab key enters a tab character.' ) . '</li>';
			$content .= '<li id="editor-keyboard-trap-help-3">' . __( 'To move away from this area, press the Esc key followed by the Tab key.' ) . '</li>';
			$content .= '<li id="editor-keyboard-trap-help-4">' . __( 'Screen reader users: when in forms mode, you may need to press the Esc key twice.' ) . '</li>';
			$content .= '</ul>';
		}

		$screen->add_help_tab(
			array(
				'id'      => 'custom_html_widget',
				'title'   => __( 'Custom HTML Widget' ),
				'content' => $content,
			)
		);
	}
}
?>