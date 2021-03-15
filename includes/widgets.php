<?php

// Creating the widget 
class Wb_widget extends WP_Widget {
  
    function __construct() {
        parent::__construct(
        
        // Base ID of your widget
        'wb_widget', 
        
        // Widget name will appear in UI
        __('Book Widget', 'wb_domain'), 
        
        // Widget description
        array( 'description' => __( 'Custom widget to display books of selected category in the sidebar', 'wb_domain' ), ) 
    );
    }
      
    // Creating widget front-end
      
    public function widget( $args, $instance ) {
        echo $args[ 'before_widget' ];?>

        <h2 class="widget-title">
                <?php
                    if ( isset( $instance[ 'title' ] ) ) {
                        $title = $instance[ 'title' ];
                    } else {
                        $title = 'Books Section';
                    }
                    echo $title;
                ?>
        </h2>

        <?php

        if ( ! empty( $instance[ 'selected_categories' ] ) && is_array( $instance[ 'selected_categories' ] ) ){ 

            global $book_options; 

            if ( $book_options[ 'num_of_books' ] == '0' ) {
                return; 
            }
            
            $posts = get_posts( array( 
                'post_type'   => 'book',
                'post_status' => 'publish',
                'numberofposts' => $book_options[ 'num_of_books' ],
                'tax_query'   => array(
                    array(
                      'taxonomy' => 'book',
                      'field'    => 'term_id', 
                      'terms'    => $instance[ 'selected_categories' ],
                    )
                  )
            ) );
            
            ?>
            
            <ul>
                <?php foreach ( $posts as $post ) { ?>
                    <li><a href="<?php echo get_permalink( $post->ID ); ?>">
                            <?php echo $post->post_title; ?>
                        </a>
                        <span class="post-date"> <?php echo get_the_date('F y, o'); ?></span>
                    </li>		
                <?php } ?>
            </ul>
            <?php 
            
        }else{
            echo esc_html__( 'No posts selected!', 'wb_domain' );	
        }
        echo $args[ 'after_widget' ];
    }
             
    // Widget Backend 
    public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
    }
    else {
        $title = __( 'Books Section', 'wb_domain' );
    }
    // Widget admin form
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php 
    $categories = get_terms( array(
        'taxonomy' => 'book',
        'hide_empty' =>false
    ));
    ?>
    <p><?php __('Choose category ', 'wb_domain') ?></p>
    <?php
        if( isset( $instance['selected_categories'])) {
            $selected_categories =$instance['selected_categories'];
        } else {
            $selected_categories = array();
        }
    ?>
    <ul>
        <?php foreach( $categories as $category) { ?>
            <li>
                <input type="checkbox" id="<?php echo $this->get_field_id('selected_categories');?>[<?php $category->term_id; ?>]"
                name="<?php echo esc_attr($this->get_field_name('selected_categories')); ?>[]"
                value="<?php echo $category->term_id; ?>"
                <?php checked((in_array($category->term_id, $selected_categories))? $category->term_id : '', $category->term_id); ?>/>
                <label for="<?php echo $this->get_field_id('selected_categories');?>[<?php echo $category->term_id; ?>]"><?php echo $category->name; ?>(<?php echo $category->count; ?>)</label>
            </li>
        <?php 
        } ?>
    </ul>
    <?php
    }
          
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $selected_categories = ( ! empty( $new_instance['selected_categories'] ) ) ? (array) $new_instance['selected_categories'] : array();
    $instance['selected_categories'] = array_map('sanitize_text_field', $selected_categories);
    return $instance;
    }
     
} 
     
     
