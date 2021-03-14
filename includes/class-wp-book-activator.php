<?php

/**
 * Fired during plugin activation
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Wp_Book
 * @subpackage Wp_Book/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Book
 * @subpackage Wp_Book/includes
 * @author     Vibhav Salelkar <salelkarvibhav@gmail.com>
 */
class Wp_Book_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$version = '1.0.0';

        $wp_book_admin = new Wp_Book_Admin( 'wp_book', $version );
        $wp_book_admin->book_create_custom_table();

	}

}
