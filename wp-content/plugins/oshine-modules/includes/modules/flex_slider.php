<?php
/**************************************
			BE IMAGE SLIDER
**************************************/
if (!function_exists('be_flex_slider')) {
	function be_flex_slider( $atts, $content, $tag ) {
		$atts =  shortcode_atts( array(
			'slide_show' => '0',
            'slide_show_speed' => 1000,
            'key' => be_uniqid_base36(true),
        ), $atts, $tag );
        extract( $atts );
	    global $be_themes_data;
		if(!isset($be_themes_data['slider_navigation_style']) || empty($be_themes_data['slider_navigation_style'])) {
			$arrow_style = 'style1-arrow';
		} else {
			$arrow_style = $be_themes_data['slider_navigation_style'];
		}
	    $slide_show = ( !empty( $slide_show ) ) ? 1 : 0 ;
        $slide_show_speed = ( !empty( $slide_show_speed ) ) ? $slide_show_speed : 4000 ;
        
        $custom_style_tag = be_generate_css_from_atts( $atts, $tag, $key );
        $css_id = be_get_id_from_atts( $atts );
		$visibility_classes = be_get_visibility_classes_from_atts( $atts );
        $data_animations = be_get_animation_data_atts( $atts );
        $unique_class_name = ' tatsu-'.$atts['key'];
        $classes = array( $unique_class_name, 'be_image_slider', 'oshine-module' );
        if( !empty( $visibility_classes ) ) {
            $classes[] = $visibility_classes;
        }
        if( isset( $animate ) && 1 == $animate && 'none' !== $animation_type ) {
            $classes[] = 'tatsu-animate';
        }
        if( !empty( $css_classes ) ) {
            $classes[] = $css_classes;
        }
        $classes[] = $arrow_style;

		
	    $output = "";
	    $output .= '<div ' . $css_id . ' class="' . implode( ' ', $classes ) . '" ' . $data_animations . ' >' . $custom_style_tag . '<div class="image_slider_module slides" data-slide-show="'.$slide_show.'" data-slide-show-speed="'.$slide_show_speed.'">';
		$output .= do_shortcode( $content );
	    // $output .= '</ul><div class="font-icon loader-style4-wrap loader-icon"></div>';
	    $output .= '</div></div>';
	    return $output;
	}
	add_shortcode( 'flex_slider', 'be_flex_slider' );
}

if (!function_exists('be_flex_slide')) {
	function be_flex_slide( $atts, $content ){
			extract( shortcode_atts( array(
				'image'=>'',
				'video'=>'',
	        	'size'=>'full',
	    	), $atts ) );

			$output = '';
	    	$output .= '<div class="be_image_slide">';
			if( ! empty( $video ) ) {	
				$videoType = be_themes_video_type( $video );
				if( $videoType == "youtube" ) {
					$video_id = ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video, $match ) ) ? $match[1] : $video_id ; 
					$output.='<iframe width="940" height="450" src="https://www.youtube.com/embed/'.$video_id.'" allowfullscreen></iframe>';
				}
				elseif( $videoType == "vimeo" ) {
					sscanf( parse_url( $video, PHP_URL_PATH ), '/%d', $video_id );
					$output.='<iframe src="https://player.vimeo.com/video/'.$video_id.'" width="500" height="281" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
				}
			} else {
				if ( !empty( $image ) ) { // check if the post has a Post Thumbnail assigned to it.
					//$attachment_info = wp_get_attachment_image_src( $image, $size );
					//$attachment_url = $attachment_info[0];
					$output .=  '<img src="'.$image.'" alt="" />';
				}
			}
	        $output .='</div>';

	        return $output;
	}
	add_shortcode( 'flex_slide', 'be_flex_slide' );
}

add_action( 'tatsu_register_modules', 'oshine_register_flex_slider');
function oshine_register_flex_slider() {
		$controls = array (
			'icon' => OSHINE_MODULES_PLUGIN_URL.'/img/modules.svg#image_slider',
			'title' => __( 'Image Slider', 'oshine-modules' ),
			'is_js_dependant' => false, //implements custom css trigger using lifecycle hooks
			'child_module' => 'flex_slide',
			'type' => 'multi',
			'initial_children' => 3,
			'is_built_in' => true,
			'group_atts' => array (
				array (
					'type'	=>	'tabs',
					'style'	=>	'style1',
					'group'	=>	array (
						array (
							'type'	=>	'tab',
							'title'	=>	__( 'Style' , 'oshine-modules'),
							'group'	=>	array (								
                                'slide_show',
                                'slide_show_speed',											
							)
                        ),
                        array (
							'type'	=>	'tab',
							'title'	=>	__( 'Advanced' , 'oshine-modules'),
							'group'	=>	array (								
							)
						),
					)
				),
			),
			'atts' => array (
			    array (
					'att_name' => 'slide_show',
					'type' => 'switch',
					'label' => __( 'Enable Slideshow', 'oshine-modules' ),
					'default' => '1',
					'tooltip' => ''
				),
				array (
					'att_name' => 'slide_show_speed',
					'type' => 'slider',
					'label' => __( 'Slide Interval', 'oshine-modules' ),
					'options' => array(
						'min' => '0',
						'max' => '10000',
						'step' => '1000',
						'unit' => 'ms',
					),	        		
                    'default' => '2000',
                    'visible' => array ( 'slide_show', '=', '1' ),
					'tooltip' => ''
				),
			),
		);
	tatsu_register_module( 'flex_slider', $controls );
}


add_action( 'tatsu_register_modules', 'oshine_register_flex_slide');
function oshine_register_flex_slide() {
		$controls = array (
			'icon' => '',
			'title' => __( 'Slide', 'oshine-modules' ),
			'is_js_dependant' => false,
			'child_module' => '',
			'type' => 'sub_module',
			'is_built_in' => true,
			'atts' => array (
				array (
					'att_name' => 'image',
					'type' => 'single_image_picker',
					'label' => __( 'Slider image', 'oshine-modules' ),
					'tooltip' => '',
				),
				array (
					'att_name' => 'video',
					'type' => 'text',
					'label' => __( 'Youtube/ Vimeo url', 'oshine-modules' ),
					'default' => '',
					'tooltip' => ''
				),
	        ),
	        'presets' => array(
	        	'default' => array(
	        		'title' => '',
	        		'image' => '',
	        		'preset' => array(
	        			'image' => 'http://placehold.it/1160x600',
	        		),
	        	)
	        ),	        
	    );
	tatsu_register_module( 'flex_slide', $controls );
}

if( !function_exists( 'oshine_modules_remove_common_atts_from_flex_slide' ) ) {
    add_filter( 'tatsu_default_common_atts_global_excludes', 'oshine_modules_remove_common_atts_from_flex_slide' );
    function oshine_modules_remove_common_atts_from_flex_slide( $excludes_array ) {
        $excludes_array[] = 'flex_slide';
        return $excludes_array;
    }
}