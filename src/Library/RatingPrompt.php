<?php
/**
 * BoldGrid Library Rating Prompt Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library\License
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library;

use Boldgrid\Library\Library;

/**
 * BoldGrid Library Rating Prompt Class.
 *
 * This class is responsible for displaying admin notices asking for feedback / a rating on wp.org.
 *
 * @since x.x.x
 */
class RatingPrompt {
	/**
	 * Whether or not we've already added the filters in the constructor.
	 *
	 * Filters only need to be added once.
	 *
	 * @since 2.7.7
	 * @var bool
	 */
	private static $filtersAdded = false;

	/**
	 * The option name where rating prompts are stored.
	 *
	 * @since 2.7.7
	 * @var string
	 */
	private $optionName = 'bglib_rating_prompt';

	/**
	 * Initialize class and set class properties.
	 *
	 * @since 2.7.7
	 */
	public function __construct() {
		// Only add the filters once.
		if ( ! self::$filtersAdded ) {
			Filter::add( $this );
			self::$filtersAdded = true;
		}

		// Debug code to delete all rating prompts.
		if( isset( $_GET['reset'] ) && '1' === $_GET['reset'] ) {
			delete_option( $this->optionName );
		}
	}

	/**
	 * Add a prompt.
	 *
	 * This only adds a prompt if it doesn't already exist by name.
	 *
	 * @since 2.7.7
	 *
	 * @param  array $prompt An array of configs for a prompt.
	 * @return bool          Whether or not the prompt was added.
	 */
	public function addPrompt( $prompt ) {
		$added = false;

		if ( ! $this->isPrompt( $prompt['name'] ) && ! $this->isPluginDismissed( $prompt['plugin'] ) ) {
			$prompt['time_added'] = time();

			$prompts = $this->getPrompts();

			$prompts[] = $prompt;

			$added = $this->savePrompts( $prompts );
		}

		return $added;
	}

	/**
	 * Add admin notices.
	 *
	 * @since 2.7.7
	 */
	public function admin_notices() {
		// testing
		$prompts = $this->getPrompts();
		// echo '<pre>PROMPTS = ' . print_r( $prompts,1) . '</pre>';

		$prompt = $this->getNext();
		// echo '<pre>NEXT PROMPT = '; print_r( $prompt ); echo '</pre>';

		$slides = $this->getPromptSlides( $prompt );
		//echo '<pre>$slides = ' . print_r( $slides,1 ) . '</pre>';

		if ( ! empty( $slides ) ) {
			echo '<div class="notice notice-success bglib-rating-prompt is-dismissible" data-slide-name="' . esc_attr( $prompt['name'] ) . '">';
			foreach ( $slides as $slide ) {
				echo $slide;
			}
			wp_nonce_field( 'bglib-rating-prompt' );
			echo '</div>';
		}
	}

	/**
	 * Dismiss a rating prompt via ajax.
	 *
	 * @since 2.7.7
	 *
	 * @hook wp_ajax_blib_rating_prompt_dismiss
	 */
	public function ajaxDismiss() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'boldgrid-backup' ) );
		}

		if( ! check_ajax_referer( 'bglib-rating-prompt', 'security', false ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'boldgrid-backup' ) );
		}

		$name          = sanitize_text_field( $_POST['name'] );
		$type          = sanitize_text_field( $_POST['type'] );
		$snooze_length = (int) $_POST['length'];

		if ( 'dismiss' === $type ) {
			$dismissed = $this->updatePromptKey( $name, 'time_dismissed', time() );
			$dismissed ? wp_send_json_success() : wp_send_json_error( 'Error dismissing prompt' );
		} else if ( 'snooze' === $type ) {
			$snoozed = $this->updatePromptKey( $name, 'time_snoozed_until', time() + $snooze_length );
			$snoozed ? wp_send_json_success() : wp_send_json_error( 'Error snoozing prompt' );
		}
	}

	/**
	 * Admin enqueue scripts.
	 *
	 * @since 2.7.7
	 */
	public function admin_enqueue_scripts() {
		$prompt = $this->getNext();

		if ( ! empty( $prompt ) ) {
			wp_enqueue_script(
				'bglib-rating-prompt-js',
				Library\Configs::get( 'libraryUrl' ) . 'src/assets/js/rating-prompt.js',
				'jquery'
			);

			wp_enqueue_style(
				'bglib-rating-prompt-css',
				Library\Configs::get( 'libraryUrl' ) . 'src/assets/css/rating-prompt.css'
			);
		}
	}

	/**
	 * Return the markup of a prompt's slides.
	 *
	 * @since 2.7.7
	 *
	 * @param  array  $prompt A prompt.
	 * @return string
	 */
	public function getPromptSlides( $prompt ) {
		$slides = array();

		if ( ! empty( $prompt['slides'] ) ) {
			foreach ( $prompt['slides'] as $slide_id => $slide ) {

				$slide_markup = '<div data-slide-id="' . esc_attr( $slide_id ) . '">';

				$slide_markup .= '<p>' . $slide['text'] . '</p>';

				if ( ! empty( $slide['decisions'] ) ) {
					$slide_decisions = array();

					foreach( $slide['decisions'] as $decision ) {
						$action = isset( $decision['snooze'] ) ? 'snooze' : 'dismiss';

						$markup = '<a data-action="' . esc_attr( $action ) . '" data-parent="0" ';

						if ( isset( $decision['link'] ) ) {
							$markup .= 'href="' . $decision['link'] . '" target="_blank" ';
						} else {
							$markup .= 'href="" ';
						}

						if ( isset( $decision['snooze'] ) ) {
							$markup .= 'data-snooze="' . esc_attr( $decision['snooze'] ) . '" ';
						}

						if ( isset( $decision['slide'] ) ) {
							$markup .= 'data-next-slide="' . esc_attr( $decision['slide'] ) . '" ';
						}

						$markup .= '>' . $decision['text'] . '</a>';

						$slide_decisions[] = $markup;
					}
					$slide_markup .= '<ul><li>' . implode( '</li><li>', $slide_decisions ) . '</li></ul>';
				}

				$slide_markup .= '</div>';

				$slides[$slide_id] = $slide_markup;
			}
		}

		// echo '<pre>$slides = ' . htmlspecialchars( print_r( $slides,1) ) . '</pre>';

		return $slides;
	}

	/**
	 * Get a prompt by name.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $name
	 * @return array
	 */
	public function getPrompt( $name ) {
		$prompt_found = array();

		$prompts = $this->getPrompts();

		foreach( $prompts as $prompt ) {
			if ( $name === $prompt['name'] ) {
				$prompt_found = $prompt;
				break;
			}
		}

		return $prompt_found;
	}

	/**
	 * Get all rating prompts.
	 *
	 * @since 2.7.7
	 *
	 * @return array
	 */
	public function getPrompts() {
		return get_option( $this->optionName, array() );
	}

	/**
	 * Determine whether or not a given plugin has any dismissed notices.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $plugin A plugin name.
	 * @return bool
	 */
	public function isPluginDismissed( $plugin ) {
		$dismissed = false;

		$plugin_prompts = $this->getPluginPrompts( $plugin );

		foreach ( $plugin_prompts as $prompt ) {
			if ( ! empty( $prompt['time_dismissed'] ) ) {
				$dismissed = true;
			}
		}

		return $dismissed;
	}

	/**
	 * Whether or not a prompt (by name) exists.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $name A prompt name.
	 * @return bool
	 */
	public function isPrompt( $name ) {
		$prompt = $this->getPrompt( $name );

		return ! empty( $prompt );
	}

	/**
	 * Get all prompts for a plugin.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $plugin The name of a plugin.
	 * @return array
	 */
	public function getPluginPrompts( $plugin ) {
		$plugin_prompts = array();

		$prompts = $this->getPrompts();

		foreach ( $prompts as $prompt ) {
			if ( $prompt['plugin'] === $plugin ) {
				$plugin_prompts[] = $prompt;
			}
		}

		return $plugin_prompts;
	}

	/**
	 * Get the next prompt to display.
	 *
	 * We only display one prompt at a time. Get that prompt.
	 *
	 * @since 2.7.7
	 */
	public function getNext() {
		$next_prompt = array();
		$prompts = $this->getPrompts();

		foreach ( $prompts as $prompt ) {
			$is_dismissed      = isset( $prompt['time_dismissed'] );
			$is_newer          = empty( $next_prompt ) || ( ! $is_dismissed && $prompt['time_added'] < $next_prompt['time_added'] );
			$is_still_snoozing = ! empty( $prompt['time_snoozed_until'] ) && $prompt['time_snoozed_until'] > time();

			if ( ! $is_dismissed && $is_newer && ! $is_still_snoozing ) {
				$next_prompt = $prompt;
			}
		}

		return $next_prompt;
	}

	/**
	 * Update an existing prompt.
	 *
	 * If the prompt is not found, this method will not make any changes.
	 *
	 * @since 2.7.7
	 *
	 * @param  array $prompt_to_save A prompt.
	 * @return bool                  Whether or not the prompt was found and updated.
	 */
	public function savePrompt( $prompt_to_save ) {
		$found = false;

		$prompts = $this->getPrompts();

		foreach ( $prompts as $key => $prompt ) {
			if ( $prompt_to_save['name'] === $prompt['name'] ) {
				$found = true;

				$prompts[$key] = $prompt_to_save;

				break;
			}
		}

		return $found ? $this->savePrompts( $prompts ) : false;
	}

	/**
	 * Save prompts.
	 *
	 * Be sure to pass in all prompts. This replaces the existing option.
	 *
	 * @since 2.7.7
	 *
	 * @param  array $prompts An array of prompts.
	 * @return bool           Whether or not the prompts were saved.
	 */
	public function savePrompts( $prompts ) {
		return update_option( $this->optionName, $prompts );
	}

	/**
	 * Update an attribute for a prompt.
	 *
	 * Commonly used to set the time a prompt was dismissed.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $name  The name of a prompt.
	 * @param  string $key   The key to update.
	 * @param  mixed  $value The new value for the key.
	 * @return bool          Whether or not the prompt was saved successfully.
	 */
	public function updatePromptKey( $name, $key, $value ) {
		$prompt = $this->getPrompt( $name );

		$prompt[$key] = $value;

		return $this->savePrompt( $prompt );
	}
}
