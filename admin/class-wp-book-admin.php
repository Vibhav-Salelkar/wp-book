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
			'singular_name'     => _x( 'Book', 'taxonomy singular name' ),
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
		add_meta_box("wb-about-book", "Book Details", array($this, "wb_about_book_meta_box"), "book", "side", "default");
	}

	/* Display the book meta box. */
	public function wb_about_book_meta_box( $post ) { ?>
	
		<?php wp_nonce_field( basename( __FILE__ ), 'wb_about_book_nonce' ); ?>
	  
		<p>
		  <label for="wb-book-author"><?php _e( "Author Name", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-author" id="wb-book-author" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wb_book_author', true ) ); ?>" size="30" />
		</p>
		<p>
		  <label for="wb-book-price"><?php _e("Price", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-price" id="wb-book-price" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wb_book_price', true ) ); ?>" size="30" />
		</p>
		<p>
		  <label for="wb-book-publisher"><?php _e( "Publisher", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-publisher" id="wb-book-publisher" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wb_book_publisher', true ) ); ?>" size="30" />
		</p>
		<p>
		  <label for="wb-book-year"><?php _e( "Year", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-year" id="wb-book-year" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wb_book_year', true ) ); ?>" size="30" />
		</p>
		<p>
		  <label for="wb-book-edition"><?php _e( "Edition", 'wb_domain' ); ?></label>
		  <br />
		  <input class="widefat" type="text" name="wb-book-edition" id="wb-book-edition" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wb_book_edition', true ) ); ?>" size="30" />
		</p>
	  <?php 
	  }
}
?>
