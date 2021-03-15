<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Wp_Book
 * @subpackage Wp_Book/admin/partials
 */

function wb_display_shortcode($args) {
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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
