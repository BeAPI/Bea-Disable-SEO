<?php
/*
 Plugin Name: Bea Disable SEO
 Version: 1.0.0
 Plugin URI: http://www.beapi.fr
 Description: Disable SEO plugin : Monster Insights, Monster Insights, Google Analyticator
 Author: BE API Technical team
 Author URI: http://www.beapi.fr

 ----

 Copyright 2016 BE API Technical team (human@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Disable_SEO
 */
class Bea_Disable_SEO {

	/**
	 * SEO plugins.
	 *
	 * @var array
	 */
	private $seo_plugins = array(
		'google-analytics-for-wordpress',
		'google-analyticator',
		'wordpress-seo',
	);

	/**
	 * Disable plugin from current blog and network.
	 *
	 * @return bool
	 */
	public function disable_plugins() {
		// If is multisite disable plugins SEO from newtwork.
		if ( is_multisite() ) {
			return $this->auto_disable_network_plugins();
		}

		return $this->auto_disable_plugin();
	}

	/**
	 * Retreive list of plugins to be disable.
	 *
	 * @return mixed|void
	 */
	private function get_plugins() {
		/**
		 * Filter to edit list of SEO plugins.
		 *
		 * @param array $this->seo_plugins The list of SEO plugins.
		 */
		return apply_filters( 'bea_disable_seo', $this->seo_plugins );
	}

	/**
	 * Disable plugins SEO from current blog and network.
	 * @return bool
	 * @author Zainoudine Soulé
	 */
	private function auto_disable_plugin() {
		// Get list plugin acitvated on current blog.
		$active_plugins = get_option( 'active_plugins', array() );

		/**
		 * No plugins activate.
		 */
		if ( empty( $active_plugins ) ) {
			return false;
		}

		$plugins = $this->get_plugins();
		foreach ( $active_plugins as $key => $plugin ) {
			$plugin_name = explode( '/', $plugin );
			if ( ! in_array( $plugin_name[0], $plugins ) ) {
				continue;
			}
			unset( $active_plugins[ $key ] );
		}

		$active_plugins = array_values( $active_plugins );
		// Update list plugins activated on current blog.
		update_option( 'active_plugins', $active_plugins );

		return true;
	}

	/**
	 * Disable plugins SEO from network.
	 *
	 * @return bool
	 * @author Zainoudine Soulé
	 */
	private function auto_disable_network_plugins() {

		// Get list plugins activated on the network.
		$active_plugins_network = get_site_option( 'active_sitewide_plugins', array() );

		/**
		 * No plugin activate on network.
		 */
		if ( empty( $active_plugins_network ) ) {
			return false;
		}

		// Scan plugin to find plugins SEO
		$plugins = $this->get_plugins();
		foreach ( $active_plugins_network as $key => $plugin ) {
			$plugin_name = explode( '/', $key );

			if ( ! in_array( $plugin_name[0], $plugins ) ) {
				continue;
			}
			unset( $active_plugins_network[ $key ] );
		}

		// Update list plugins acticated on the network.
		update_site_option( 'active_sitewide_plugins', $active_plugins_network );
		return true;
	}
}

/**
 * We check if we are in a development environment
 * befor loading Disable_SEO class.
 */
if ( defined( 'WP_ENV' ) && 'prod' !== WP_ENV ) {
	$disable_seo = new Bea_Disable_SEO();
	$disable_seo->disable_plugins();
}
