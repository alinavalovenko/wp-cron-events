<?php
if ( function_exists( 'acf_add_local_field_group' ) ):

	acf_add_local_field_group( array(
		'key'                   => 'group_5cc163235f0b9',
		'title'                 => 'GZIP Compression for external url',
		'fields'                => array(
			array(
				'key'               => 'field_5cc1633d29bf7',
				'label'             => 'Hotjar',
				'name'              => 'hotjar_url',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_5cc163b829bf8',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'wrapper'           => array(
					'width' => '25',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '//static.hotjar.com/',
				'placeholder'       => '//static.hotjar.com/',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5cc1642f29bfb',
				'label'             => 'GTM',
				'name'              => 'googletagmanager_url',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_5cc163b829bf8',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'wrapper'           => array(
					'width' => '25',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '//www.googletagmanager.com/',
				'placeholder'       => '//www.googletagmanager.com/',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5cc1642e29bfa',
				'label'             => 'Facebook',
				'name'              => 'facebook_url',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_5cc163b829bf8',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'wrapper'           => array(
					'width' => '25',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '//connect.facebook.net/it_IT/sdk.js',
				'placeholder'       => '//connect.facebook.net/it_IT/sdk.js',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5cc1642d29bf9',
				'label'             => 'Google Analitics',
				'name'              => 'google_analitics_url',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_5cc163b829bf8',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'wrapper'           => array(
					'width' => '25',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '//www.google-analytics.com/analytics.js',
				'placeholder'       => '//www.google-analytics.com/analytics.js',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5cc163b829bf8',
				'label'             => 'Enable GZIP for external resources?',
				'name'              => 'enable_gzip',
				'type'              => 'true_false',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'message'           => 'Yes',
				'default_value'     => 0,
				'ui'                => 0,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'theme-general-settings',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => 1,
		'description'           => '',
	) );

	add_filter( 'cron_schedules', 'vsmo_register_new_cron_time' );
	function vsmo_register_new_cron_time( $schedules ) {
		$schedules['minute']    = array(
			'interval' => 60,
			'display'  => 'Every minute'
		);
		$schedules['fifteen']   = array(
			'interval' => 60 * 15,
			'display'  => 'Every 15 minutes'
		);
		$schedules['twenty']    = array(
			'interval' => 60 * 20,
			'display'  => 'Every 20 minutes'
		);
		$schedules['two_hours'] = array(
			'interval' => 60 * 60 * 2,
			'display'  => 'Every 2 hours'
		);

		return $schedules;
	}

	add_action( 'init', 'vsmo_enable_gzip_compression' );
	function vsmo_enable_gzip_compression() {
		$f = get_field( 'enable_gzip', 'option' );
		if ( $f ) {
			if ( ! wp_next_scheduled( 'download_hotjar_script' ) ) {
				wp_schedule_event( time(), 'minute', 'download_hotjar_script' );
			}
			if ( ! wp_next_scheduled( 'download_gtm_script' ) ) {
				wp_schedule_event( time(), 'fifteen', 'download_gtm_script' );
			}
			if ( ! wp_next_scheduled( 'download_fb_script' ) ) {
				wp_schedule_event( time(), 'twenty', 'download_fb_script' );
			}
			if ( ! wp_next_scheduled( 'download_ga_script' ) ) {
				wp_schedule_event( time(), 'two_hours', 'download_ga_script' );
			}
		}
	}

	add_action( 'download_hotjar_script', 'download_hotjar_script' );
	function download_hotjar_script() {
		$hotjar_url = get_field( 'hotjar_url', 'option' );
		if ( ! empty( $hotjar_url ) ) {
			$filename = get_data_by_url( $hotjar_url );
			update_option( 'hotjar_local_script', $filename, 'no' );
		}
	}

	add_action( 'download_gtm_script', 'download_gtm_script' );
	function download_gtm_script() {
		$gtm_url = get_field( 'googletagmanager_url', 'option' );
		if ( ! empty( $gtm_url ) ) {
			$filename = get_data_by_url( $gtm_url );
			update_option( 'gtm_url_local_script', $filename, 'no' );
		}
	}

	add_action( 'download_fb_script', 'download_fb_script' );
	function download_fb_script() {
		$facebook_url = get_field( 'facebook_url', 'option' );
		if ( ! empty( $facebook_url ) ) {
			$filename = get_data_by_url( $facebook_url );
			update_option( 'facebook_url_local_script', $filename, 'no' );
		}
	}

	add_action( 'download_ga_script', 'download_ga_script' );
	function download_ga_script() {
		$ga = get_field( 'google_analitics_url', 'option' );
		if ( ! empty( $ga ) ) {
			$filename = get_data_by_url( $ga );
			update_option( 'ga_url_local_script', $filename, 'no' );
		}
	}

	function get_data_by_url( $url ) {
		$protocol = stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://';
		$path     = str_replace( '//', $protocol, $url );
		$data     = file_get_contents( $path );
		$filename = sanitize_file_name( $url ) . '.js';
		$path     = get_home_path() . $filename;
		$f        = file_put_contents( $path, $data );
		if ( ! $f ) {
			update_option( 'gzip_log', "Cannot save data from " . $url . ' to the file ' . $path, 'no' );
		}

		return $filename;
	}
endif;