<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Wp_Book
 * @subpackage Wp_Book/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Book
 * @subpackage Wp_Book/admin
 * @author     Vibhav Salelkar <salelkarvibhav@gmail.com>
 */

 /***
  * includes
  */
//for widgets.php
 require_once( plugin_dir_path( dirname( __FILE__ )).'includes/widgets.php');

 class Wp_Book_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Book_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Book_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-book-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Book_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Book_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-book-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function wb_custom_post_type() {
		register_post_type('book',
			array(
				'labels'      => array(
					'name'          => __( 'Books', 'textdomain' ),
					'singular_name' => __( 'Book', 'textdomain' ),
				),
				'public'      => true,
				'has_archive' => true,
				'rewrite'     => array( 'slug' => 'book' ), 
			)
		);
	}

	public function wb_register_taxonomy_book() {
		$labels = array(
			'name'              => _x( 'Book Category', 'taxonomy general name' ),
			'singular_name'     => _x( 'Book Category', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Book Categories' ),
			'all_items'         => __( 'All Book Categories' ),
			'parent_item'       => __( 'Parent Book Category' ),
			'parent_item_colon' => __( 'Parent Book Category:' ),
			'edit_item'         => __( 'Edit Book Category' ),
			'update_item'       => __( 'Update Book Category' ),
			'add_new_item'      => __( 'Add New Book Category' ),
			'new_item_name'     => __( 'New Book Category Name' ),
			'menu_name'         => __( 'Book' ),
		);
		$args   = array(
			'hierarchical'      => true, // make it hierarchical (like categories)
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => true,    //required to see taxonomy in ui
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'book' ],
		);
		register_taxonomy( 'book', [ 'book' ], $args );
	}

	public function wb_nonhierarchical_taxonomy_book() {
		$labels = array(
		  'name' => _x( 'Book Tag', 'taxonomy general name' ),
		  'singular_name' => _x( 'Book Tag', 'taxonomy singular name' ),
		  'search_items' =>  __( 'Search Book Tag' ),
		  'popular_items' => __( 'Popular Book Tags' ),
		  'all_items' => __( 'All Book Tags' ),
		  'parent_item' => null,
		  'parent_item_colon' => null,
		  'edit_item' => __( 'Edit Book Tag' ), 
		  'update_item' => __( 'Update Book Tag' ),
		  'add_new_item' => __( 'Add New Book Tag' ),
		  'new_item_name' => __( 'New Book Tag' ),
		  'separate_items_with_commas' => __( 'Separate book tags with commas' ),
		  'add_or_remove_items' => __( 'Add or remove book tags' ),
		  'choose_from_most_used' => __( 'Choose from the most used book tags' ),
		  'menu_name' => __( 'Book Tags' ),
		); 
	   
		register_taxonomy('book tags','book',array(
		  'hierarchical' => false,
		  'labels' => $labels,
		  'show_ui' => true,
		  'show_in_rest' => true,
		  'show_admin_column' => true,
		  'update_count_callback' => '_update_post_term_count',
		  'query_var' => true,
		  'rewrite' => array( 'slug' => 'book-tag' ),
		));
	}

	/* Meta box setup function. */
	public function wb_book_meta_boxes_setup() {
		/* Add meta boxes on the 'add_meta_boxes' hook. Note add_action here wont work so I used this alternate way */
		add_meta_box("wb-about-book", __('Book Details', 'wb_domain'), array($this, "wb_about_book_meta_box"), "book", "side", "default");
	}

	/* Display the book meta box. */
	public function wb_about_book_meta_box( $post ) { 

			$all_details= get_metadata('bookdetails',$post->ID,'_book_details_key')[0];
		?>
	
		<?php wp_nonce_field( basename( __FILE__ ), 'wb_about_book_nonce' ); ?>
	  
		<p>
		  <label for="wb-book-author"><?php _e( "Author Name", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-author" id="wb-book-author" value="<?php echo esc_attr($all_details['author_name']); ?>" size="30" />
		</p>
		<p>
		  <label for="wb-book-price"><?php _e("Price", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-price" id="wb-book-price" value="<?php echo esc_attr($all_details['price']); ?>" size="30" />
		</p>
		<p>
		  <label for="wb-book-publisher"><?php _e( "Publisher", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-publisher" id="wb-book-publisher" value="<?php echo esc_attr($all_details['publisher']); ?>" size="30" />
		</p>
		<p>
		  <label for="wb-book-year"><?php _e( "Year", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-year" id="wb-book-year" value="<?php echo esc_attr($all_details['year']); ?>" size="30" />
		</p>
		<p>
		  <label for="wb-book-edition"><?php _e( "Edition", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-edition" id="wb-book-edition" value="<?php echo esc_attr($all_details['edition']); ?>" size="30" />
		</p>
	  <?php 
	  }

	  public function wb_save_book_details( $post_id ) {
		
		if(! isset( $_POST['wb_about_book_nonce']) || !wp_verify_nonce( $_POST['wb_about_book_nonce'], basename( __FILE__ ) )){
			return $post_id;
		}

		$author_name = sanitize_text_field( $_POST[ 'wb-book-author' ] );
        $price       = sanitize_text_field( $_POST[ 'wb-book-price' ] );
        $publisher   = sanitize_text_field( $_POST[ 'wb-book-publisher' ] );
        $year        = sanitize_text_field( $_POST[ 'wb-book-year' ] );
        $edition     = sanitize_text_field( $_POST[ 'wb-book-edition' ] );

		$all_info = array(
            'author_name' => $author_name,
            'price'       => $price,
            'publisher'   => $publisher,
            'year'        => $year,
            'edition'     => $edition
        );

		update_metadata( 'bookdetails', $post_id, '_book_details_key', $all_info );

	  }

	  public function book_create_custom_table() {

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'book_details_meta';

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            $query = "CREATE TABLE " . 
                $table_name . "(
                meta_id bigint(20) NOT NULL AUTO_INCREMENT,
                bookdetails_id bigint(20) NOT NULL DEFAULT '0',
                meta_key varchar(255) DEFAULT NULL,
                meta_value longtext,
                PRIMARY KEY  (meta_id),
                KEY bookdetails_id (bookdetails_id),
                KEY meta_key (meta_key)
            )" . $charset_collate . ";";

            dbDelta( $query );
        }
	  }

	  public function wb_register_custom_table() {

        global $wpdb;

        $wpdb->bookdetailsmeta = $wpdb->prefix . 'book_details_meta';
        $wpdb->tables[] = 'book_details_meta';
        
        return;
    }

	public function wb_custom_settings_page() {
 
        add_menu_page( __( 'Book Settings', 'wb_domain' ), __( 'Book Settings', 'wb_domain' ), 'manage_options', 'book-settings', array($this,'wb_render_settings_page'),
            'dashicons-admin-page');
    }

	public function wb_register_settings() {

		register_setting( 'book-settings-group', 'book_settings' );
        register_setting( 'book-widget-settings-group', 'book_widget_settings' );
    }

	public function wb_render_settings_page() {
		global $book_options;
	   
		$currencies = array( 'INR', 'USD', 'EUR' ); ?>
		
		<h2><?php _e( 'Books Menu', 'wb_domain' ); ?></h2>

			<form method="post" action="options.php">
		   
			<?php settings_fields( 'book-settings-group' ); ?>
			
			<div>
			<p>
				<label class="description" for="book_settings[currency]"> <?php _e( 'Select currency', 'wb_domain' ); ?>: </label>
			   
				<select id="book_settings[currency]" name="book_settings[currency]">
				<?php
					$selected_currency = esc_attr( $book_options[ 'currency' ] );
				foreach ( $currencies as $currency ) {
					if ( $selected_currency != $currency ) {
						echo '<option value="' . $currency . '">' . $currency . '</option>';
					} else {
						echo '<option selected value="' . $currency . '">' . $currency . '</option>';
					}
				}
				?>
				</select>
			</p>
			<p>
				<label class="description" for="book_settings[num_of_books]"> <?php _e( 'Number of Books Per Page', 'wb_domain' ); ?>: </label>
				<input type="number" min="0" max="100" id="book_settings[num_of_books]" name="book_settings[num_of_books]" value="<?php esc_attr_e( $book_options[ 'num_of_books' ] ); ?>"/>
			</p>
			<p class="submit">
				<input type="submit" class="button-primary" value="Save Options" />
			</p>
		   
			</div>
		</form>
		<?php
	}

	public function wb_render_shortcode( $attr ) {
		$attr = shortcode_atts(
			array(
				'id'=>'',
				'author_name'=>'',
				'year'=>'',
				'category'=>'',
				'tag'=>'',
				'publisher'=>''
			),
			$attr, 
			'book'
		);

		$args = array(
			'post_type' => 'book',
			'post_status' => 'publish',
			'author' => $attr['author_name']
		);

		if($attr['id'] != '') {
			$args['id'] = $attr['id'];
		}

		if($attr['category'] != '') {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'book',
					'terms' => array( $attr['category'] ),
					'field' => 'name',
					'operator' => 'IN'
				)
			);
		}

		if($attr['tag'] != '') {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'book tags',
					'terms' => array($attr['tag']),
					'field' => 'name',
					'operator' => 'IN'
				)
			);
		}

		return wb_display_shortcode($args);
	}

	public function wb_display_shortcode($args) {
		$wb_query = new WP_Query( $args );

		if( $wb_query->have_posts() ) {
			while($wb_query->have_posts()) {
				$wb_query->the_post();

				$wb_author_name = get_metadata('bookdetails', get_the_id(), '_book_details_key')[0]['author_name'];
				$wb_year = get_metadata('bookdetails', get_the_id(), '_book_details_key')[0]['year'];
				$wb_publisher = get_metadata('bookdetails', get_the_id(), '_book_details_key')[0]['publisher'];
				
				?>
				<ul>
					<?php
					if(get_the_title() != ''){
						?>
						<li>Title: <a href="<?php get_post_permalink();?>"><?php echo get_the_title();?></a></li>
						<?php
					}
					?>
					<?php
					if($wb_author_name != ''){
						?>
						<li>Author: <?php echo $wb_author_name?></li>
						<?php
					}
					?>
					<?php
					if($wb_year != ''){
						?>
						<li>Year: <?php echo $wb_year ?></li>
						<?php
					}
					?>
					<?php
					if($wb_publisher != ''){
						?>
						<li>Publisher: <?php echo $wb_publisher?></li>
						<?php
					}
					?>
					<?php
					if( get_the_content() != ''){
						?>
						<li>Content: <?php echo get_the_content();?></li>
						<?php
					}
					?>
				</ul>
				<?php
			}
			wp_reset_postdata();
		}else {
			?>
			<p>No books Found</p>
			<?php
		}
		wp_reset_query();
	}

	public function wb_register_shortcode() {
		add_shortcode( 'book', array($this,'wb_render_shortcode') );
	}

	public function wb_register_widget() {
		register_widget('Wb_widget');
	}

}
?>
