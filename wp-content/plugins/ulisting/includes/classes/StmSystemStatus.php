<?php

namespace uListing\Classes;

class StmSystemStatus {

	/**
	 * @return array
	 */
	public function get_theme_info_data() {

		$theme_active = wp_get_theme();

		if ( is_child_theme() ) {
			$theme_parent      = wp_get_theme( $theme_active->template );
			$theme_parent_info = array(
				'parent_name'           => $theme_parent->name,
				'parent_version'        => $theme_parent->version,
				'parent_author_url'     => $theme_parent->{'Author URI'},
			);
		} else {
			$theme_parent_info = array(
				'parent_name'           => '',
				'parent_version'        => '',
				'parent_version_latest' => '',
				'parent_author_url'     => '',
			);
		}

		$override_files     = array();
		$outdated_templates = false;
		$scan_files         = apply_filters("ulisting_template_status_scan_files", StmSystemStatus::template_files_scan(ULISTING_PATH . '/templates' ));

		foreach ( $scan_files as $file ) {
			$located = apply_filters( 'stm_get_template', $file, $file, array(), StmListingTemplate::template_path(), StmListingTemplate::plugin_path() . '/templates' );

			if ( file_exists( $located ) ) {
				$theme_file = $located;
			} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_stylesheet_directory() . '/' . StmListingTemplate::template_path() . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . StmListingTemplate::template_path() . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . StmListingTemplate::template_path() . $file ) ) {
				$theme_file = get_template_directory() . '/' . StmListingTemplate::template_path() . $file;
			} else {
				$theme_file = false;
			}

			if ( ! empty( $theme_file ) ) {
				$plugin_version  = StmSystemStatus::get_version_file( StmListingTemplate::plugin_path() ."/". $file );
				$theme_version = StmSystemStatus::get_version_file($theme_file);
				if ( $plugin_version && ( empty( $theme_version ) || version_compare( $theme_version, $plugin_version, '<' ) ) ) {
					if ( ! $outdated_templates ) {
						$outdated_templates = true;
					}
				}
				$override_files[] = array(
					'file'          => str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ),
					'theme_version' => $theme_version,
					'plugin_version'  => $plugin_version,
				);
			}
		}


		$active_theme_info = array(
			'theme_name'                    => $theme_active->name,
			'theme_version'                 => $theme_active->version,
			'theme_author_url'              => esc_url_raw( $theme_active->{'Author URI'} ),
			'is_child_theme'          => is_child_theme(),
			'has_outdated_templates'  => $outdated_templates,
			'overrides'               => $override_files,
		);
		return array_merge( $active_theme_info, $theme_parent_info );
	}

	/**
	 * @param $template_path
	 *
	 * @return array
	 */
	public static function template_files_scan( $template_path ) {
		$files  = @scandir( $template_path );
		$result_data = array();

		if ( empty( $files ) )
			return array();

		foreach ( $files as $key => $value ) {
			if ( ! in_array( $value, array( '.', '..' ), true ) ) {
				if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
					$files_sub = self::template_files_scan( $template_path . DIRECTORY_SEPARATOR . $value );
					foreach ( $files_sub as $file_sub ) {
						$result_data[] = $value . DIRECTORY_SEPARATOR . $file_sub;
					}
				} else {
					$result_data[] = $value;
				}
			}
		}

		return $result_data;
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public static function get_version_file( $file ) {
		if ( ! file_exists( $file ) )
			return '';

		$fopen = fopen( $file, 'r' );
		$data_file = fread( $fopen, 8192 );
		fclose( $fopen );
		$data_file = str_replace( "\r", "\n", $data_file );
		$version   = '';
		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $data_file, $match ) && $match[1] ) {
			$version = _cleanup_header_comment( $match[1] );
		}
		return $version;
	}
}

