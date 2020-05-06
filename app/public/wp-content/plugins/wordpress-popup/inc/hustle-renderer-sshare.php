<?php
/**
 * Class Hustle_Renderer_Sshare
 * Used to render Social sharing modules.
 *
 * @since 4.0
 */
class Hustle_Renderer_Sshare extends Hustle_Renderer_Abstract {

	const SSHARE_SHORTCODE_CSS_CLASS = 'hustle_sshare_module_shortcode_wrap';
	const SSHARE_WIDGET_CSS_CLASS = 'hustle_sshare_module_widget_wrap';

	// This is the same as embeddeds at the moment.
	const AFTERCONTENT_CSS_CLASS = 'hustle_module_after_content_wrap';

	/**
	 * Get the content container tag.
	 *
	 * @since 4.0
	 * @return string
	 */
	protected function get_wrapper_main( $subtype, $custom_classes ) {

		$module = $this->module;
		$module_id = $module->module_id;
		$display = $module->display;

		// Tracking enabled data
		$tracking_enabled_data = $module->is_tracking_enabled( $subtype ) ? 'enabled' : 'disabled';

		// Type of module
		// Applies for all except "float" modules.
		$module_type = 'inline';

		// Main data attributes for module
		$module_data = sprintf(
			'
			data-id="%s"
			data-render-id="%d"
			data-tracking="%s"
			data-sub-type="%s"
			',
			$module_id,
			self::$render_ids[ $module_id ],
			$tracking_enabled_data,
			esc_attr( $subtype )
		);

		// If this instance is a floating module.
		// This happens when floating is enabled in either desktop or mobile.
		if ( Hustle_SShare_Model::FLOAT_MODULE === $subtype ) {

			$module_type = 'float';

			// Check if the module has floating active for desktop.
			if ( self::$is_preview ) {
				$module_data .= 'data-desktop="true"';
			} elseif ( $this->module->is_display_type_active( Hustle_SShare_Model::FLOAT_DESKTOP ) ) {

				$offset = 'css_selector' === $display->float_desktop_offset ? 'selector' : $display->float_desktop_offset;
				$selector = 'selector' === $offset ? trim( $display->float_desktop_css_selector ) : '';
				$position_x = $display->float_desktop_position;
				$position_y = $display->float_desktop_position_y;

				$module_data .= sprintf(
					'
					data-desktop="true"
					data-desktop-offset="%s"
					data-desktop-selector="%s"
					data-desktop-positionX="%s"
					data-desktop-positionY="%s"
					',
					$offset,
					esc_attr( $selector ),
					esc_attr( $position_x ),
					esc_attr( $position_y )
				);
			} else {
				$module_data .= 'data-desktop="false"';
			}

			// Check if the module has floating active for mobile.
			if ( $this->module->is_display_type_active( Hustle_SShare_Model::FLOAT_MOBILE ) ) {

				$offset = 'css_selector' === $display->float_mobile_offset ? 'selector' : $display->float_mobile_offset;
				$selector = 'selector' === $offset ? trim( $display->float_mobile_css_selector ) : '';
				$position_x = $display->float_mobile_position;
				$position_y = $display->float_mobile_position_y;

				$module_data .= sprintf(
					'
					data-mobiles="true"
					data-mobiles-offset="%s"
					data-mobiles-selector="%s"
					data-mobiles-positionX="%s"
					data-mobiles-positionY="%s"
					',
					$offset,
					esc_attr( $selector ),
					esc_attr( $position_x ),
					esc_attr( $position_y )
				);
			} else {
				$module_data .= 'data-mobiles="false"';
			}
		} else {

			$module_data .= sprintf(
				'
				data-delay="%s"
				data-intro="%s"
				',
				'0',
				'no_animation'
			);

			if ( Hustle_SShare_Model::INLINE_MODULE === $subtype ) {

				$module_data .= sprintf(
					'data-alignment="%s"',
					$display->inline_align
				);
			}
		}

		$inline_style = ! self::$is_preview ? 'style="opacity:0;"' : 'style="opacity:1;"';

		$html = sprintf(
			'<div class="hustle-ui hustle-%s hustle_module_id_%d %s" %s %s>',
			$module_type,
			$module->module_id,
			esc_attr( $custom_classes ),
			$module_data,
			$inline_style
		);

		return $html;

	}

	/**
	 * Get the wrapper content.
	 *
	 * @since 4.0
	 *
	 * @param string $subtype
	 * @return string
	 */
	protected function get_wrapper_content( $subtype ) {

		$module_type = 'inline';

		if ( Hustle_SShare_Model::FLOAT_MODULE === $subtype ) {
			$module_type = 'float';
		}

		$html = sprintf(
			'<div class="hustle-%s-content">',
			$module_type
		);

		return $html;

	}

	/**
	 * Get the body content.
	 *
	 * @since 4.0
	 *
	 * @param string $subtype
	 * @return string
	 */
	protected function get_module_body( $subtype ) {

		$html = '';

		$module = $this->module;
		$module_id = $module->module_id;
		// Prevent php error messages on wizard preview when no services are active.
		$content = $module->content; //$module->__get( 'content' );
		$display = $module->display;
		$design = $module->design;

		$icons_design = $design->icon_style;
		$icons_custom_color = 'false';
		$icons_grid_desktop = 'inline';
		$icons_grid_mobiles = 'inline';
		$icons_counter = 'none';
		$icons_animated = false;
		$icons_animation = 'zoom';

		if ( 'flat' === $icons_design ) {
			$icons_design = 'default';
		} elseif ( 'outline' === $icons_design ) {
			$icons_design = 'outlined';
		}

		if ( Hustle_SShare_Model::FLOAT_MODULE === $subtype ) {

			// Check if icons custom color is enabled
			$icons_custom_color = ( '1' === $design->floating_customize_colors ) ? 'true' : 'false';

			// Check if the module has floating active for desktop.
			if ( $module->is_display_type_active( Hustle_SShare_Model::FLOAT_DESKTOP ) ) {

				$position_x = $display->float_desktop_position;
				$position_y = $display->float_desktop_position_y;

				if ( 'center' !== $display->float_desktop_position ) {
					$icons_grid_desktop = 'stacked';
				}

				if ( 'center' === $position_x ) {

					if ( 'top' === $position_y ) {
						$icons_animation = 'bounceDownUp';
					} else {
						$icons_animation = 'bounceUpDown';
					}
				}
			}

			// Check if the module has floating active for mobile.
			if ( $module->is_display_type_active( Hustle_SShare_Model::FLOAT_MOBILE ) ) {

				$position_x = $display->float_mobile_position;
				$position_y = $display->float_mobile_position_y;

				if ( 'center' !== $display->float_mobile_position ) {
					$icons_grid_mobiles = 'stacked';
				}

				if ( 'center' === $position_x ) {

					if ( 'top' === $position_y ) {
						$icons_animation = 'bounceDownUp';
					} else {
						$icons_animation = 'bounceUpDown';
					}
				}
			}

			if ( self::$is_preview ) {
				$icons_grid_desktop = 'stacked';
				$icons_grid_mobiles = 'stacked';
			}

			// Check if counter is enabled
			if ( '1' === $content->counter_enabled ) {
				$icons_counter = ( '1' === $design->floating_inline_count ) ? 'inline' : 'stacked';
			}

			// Check if icons are animated
			if ( '1' === $design->floating_animate_icons ) {
				$icons_animated = true;
			}
		} else {

			// Check if icons custom color is enabled
			$icons_custom_color = ( '1' === $design->widget_customize_colors ) ? 'true' : 'false';

			// Check if counter is enabled
			if ( '1' === $content->counter_enabled ) {
				$icons_counter = ( '1' === $design->widget_inline_count ) ? 'inline' : 'stacked';
			}

			// Check if icons are animated
			if ( '1' === $design->widget_animate_icons ) {
				$icons_animated = true;
			}
		}

		$html .= sprintf(
			'<div class="hustle-social hustle-social--%s" data-custom="%s" data-grid-desktop="%s" data-grid-mobiles="%s">',
			$icons_design,
			$icons_custom_color,
			$icons_grid_desktop,
			$icons_grid_mobiles
		);

			$html .= sprintf(
				'<ul class="hustle-counter--%s%s"%s>',
				$icons_counter,
				( true === $icons_animated ? ' hustle-animated' : '' ),
				( true === $icons_animated ? ' data-animation="' . $icons_animation . '"' : '' )
			);

				$social_icons = $content->social_icons;

				// phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact
				// Extra indentation to mimic html tree.
				if ( ! empty( $social_icons ) ) {

					foreach ( $social_icons as $icon => $data ) {

						$type  = isset( $data['type'] ) ? $data['type'] : '';
						$label = isset( $data['label'] ) ? $data['label'] : '';

						if ( '' === $data['link'] && 'email' !== $icon ) {

							$href_value = 'href="#"';
							$link_type  = 'native';

						} else {
							// Check if is email to insert mailto.
							if ( 'email' === $icon ) {

								$query_args = [
									'subject' => rawurlencode( Opt_In_Utils::replace_global_placeholders( $data['title'] ) ),
									'body'    => rawurlencode( Opt_In_Utils::replace_global_placeholders( $data['message'] ) ),
								];
								$mail_url   = add_query_arg( $query_args, 'mailto:' );
								$href_value = 'href="' . esc_url( $mail_url ) . '"';
								$title      = apply_filters( 'hustle_social_share_platform_title', rawurlencode( html_entity_decode( esc_html( get_the_title() ) ) ) );

							} else {
								$href_value = 'href="' . esc_url( $data['link'] ) . '" target="_blank"';
							}

							$link_type = 'custom';

						}

						if ( 'fivehundredpx' === $icon ) {
							$icon = '500px';
						}

						$html .= '<li>';

							$html .= sprintf(
								'<a %1$s class="hustle-share-icon hustle-share--%2$s" data-network="%3$s" data-counter="%4$s" data-link="%5$s" data-count="%6$s">',
								$href_value,
								esc_attr( $icon ),
								esc_attr( $icon ),
								'0' === $content->counter_enabled ? 'none' : esc_attr( $type ),
								$link_type,
								esc_attr( $data['counter'] )
							);

								$html .= sprintf(
									'<i class="hustle-icon-social-%s" aria-hidden="true"></i>',
									esc_attr( $icon )
								);

								if ( '1' === $content->counter_enabled ) {

									if ( 'native' === $data['type'] && ! self::$is_preview ) {
										$counter_content  = '<i class="hustle-icon-loader hustle-loading-icon" aria-hidden="true"></i>';

									} else {
										$counter_content    = ( '' !== $data['counter'] ) ? esc_attr( $data['counter'] ) : '0';
									}

									$html .= sprintf(
										'<span class="hustle-counter" aria-hidden="true">%s</span>',
										$counter_content
									);
								}

								$html .= sprintf(
									'<span class="hustle-screen-reader">Share on %s</span>',
									esc_html( $label )
								);

							$html .= '</a>';

						$html .= '</li>';

					}
				}

			// phpcs:enable Generic.WhiteSpace.ScopeIndent.IncorrectExact
			$html .= '</ul>';

		$html .= '</div>';

		return $html;

	}

	/**
	 * Handle AJAX display
	 *
	 * @since 4.0
	 * @return string
	 */
	public function ajax_display( Hustle_Module_Model $module, $data = array(), $is_preview = true ) {

		self::$is_preview = $is_preview;

		if ( ! empty( $data ) ) {
			$this->module = $module->load_preview( $data );
		} else {
			$this->module = $module->load();
		}

		$response = array(
			'style'     => array(),
			'script'    => array(),
			'module'    => $this->module,
		);

		$response['floatingHtml'] = $this->get_module( Hustle_SShare_Model::FLOAT_MODULE, 'hustle-show', $is_preview );
		$response['widgetHtml'] = $this->get_module( Hustle_SShare_Model::WIDGET_MODULE, 'hustle-show', $is_preview );

		// This might be used later for ajax loading.
		ob_start();
		$this->print_styles( $is_preview );
		$styles = ob_get_clean();
		$response['style'] = $styles;

		return $response;
	}

}
