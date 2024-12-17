<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://soyuz.com.br
 * @since      1.0.0
 *
 * @package    Sz_Conectar_Idb
 * @subpackage Sz_Conectar_Idb/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sz_Conectar_Idb
 * @subpackage Sz_Conectar_Idb/includes
 * @author     Soyuz <info@soyuz.com.br>
 */
class Sz_Conectar_Idb_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sz-conectar-idb',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
