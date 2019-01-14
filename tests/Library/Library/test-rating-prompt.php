<?php
/**
 * BoldGrid Library Rating Prompt Test Class
 *
 * @package Boldgrid\Library
 * @subpackage \Library\License
 *
 * @version 2.7.7
 * @author BoldGrid <wpb@boldgrid.com>
 */

use Boldgrid\Library\Library;

/**
 * BoldGrid Library Rating Prompt Class.
 *
 * This class is responsible for displaying admin notices asking for feedback / a rating on wp.org.
 *
 * @since 2.7.7
 */
class Test_Rating_Prompt extends WP_UnitTestCase {

	public $existingPrompt;

	public $prompts;

	public $newPrompt;

	public $ratingPrompt;

	/**
	 *
	 */
	public function reset() {
		$allowed_tags = array(
			'a' => array(
				'href'   => array(),
				'target' => array(),
			),
		);

		$lang = array(
			'feel_good_value' => __( 'If you feel you\'re getting really good value from the BoldGrid Backup plugin, could you do us a favor and rate us 5 stars on WordPress?', 'boldgrid-backup' ),
		);

		$default_prompt = array(
			'plugin' => 'boldgrid-backup',
			'name'   => 'REPLACE_THIS_NAME',
			'slides' => array(
				'start'       => array(
					'text'      => $lang['feel_good_value'],
					'decisions' => array(
						'sure_will'           => array(
							'text'  => __( 'Yes, I sure will!', 'boldgrid-backup' ),
							'link'  => 'https://wordpress.org/support/plugin/boldgrid-backup/reviews/',
							'slide' => 'thanks',
						),
						'maybe_still_testing' => array(
							'text'   => __( 'Maybe later, I\'m still testing the plugin.', 'boldgrid-backup' ),
							'snooze' => WEEK_IN_SECONDS,
							'slide'  => 'maybe_later',
						),
						'already_did'         => array(
							'text'  => __( 'I already did', 'boldgrid-backup' ),
							'slide' => 'already_did',
						),
					),
				),
				'thanks'      => array(
					'text' => sprintf(
						wp_kses(
							/* translators: The URL to the boldgrid-backup plugin in the plugin repo. */
							__( 'Thanks! A new page should have opened to the BoldGrid Backup ratings page on WordPress.org. You will need to log in to your WordPress.org account before you can post a review. If the page didn\'t open, please click the following link: <a href="%1$s" target="_blank">%1$s</a>', 'boldgrid-backup' ),
							$allowed_tags
							),
						'https://wordpress.org/support/plugin/boldgrid-backup/reviews/'
						),
				),
				'maybe_later' => array(
					'text' => sprintf(
						wp_kses(
							/* translators: The URL to submit boldgrid-backup bug reports and feature requests. */
							__( 'No problem, maybe now is not a good time. We want to be your WordPress backup plugin of choice. If you\'re experiencing a problem or want to make a suggestion, please %1$sclick here%2$s.', 'boldgrid-backup' ),
							$allowed_tags
							),
						'<a href="https://www.boldgrid.com/feedback" target="_blank">',
						'</a>'
						),
				),
				'already_did' => array(
					'text' => sprintf(
						wp_kses(
							/* translators: The URL to submit boldgrid-backup bug reports and feature requests. */
							__( 'Thank you for the previous rating! You can help us to continue improving the BoldGrid Backup plugin by reporting any bugs or submitting feature requests %1$shere%2$s. Thank you for using the BoldGrid Backup plugin!', 'boldgrid-backup' ),
							$allowed_tags
							),
						'<a href="https://www.boldgrid.com/feedback" target="_blank">',
						'</a>'
						),
				),
			),
		);

		// Set a title or description for your backup.
		$title_description_prompt                            = $default_prompt;
		$title_description_prompt['name']                    = 'update_title_description';
		$title_description_prompt['slides']['start']['text'] = __( 'We hope that you\'re finding adding titles and descriptions to your backups helpful in keeping things organized.', 'boldgrid-backup' ) . ' ' . $lang['feel_good_value'];

		// Download a backup to your local machine.
		$download_prompt                            = $default_prompt;
		$download_prompt['name']                    = 'download_to_local_machine';
		$download_prompt['slides']['start']['text'] = __( 'We\'re glad to see you\'re keeping your backups safe and downloading them to your local machine!', 'boldgrid-backup' ) . ' ' . $lang['feel_good_value'];

		// Create any type of backup.
		$any_backup_prompt                            = $default_prompt;
		$any_backup_prompt['name']                    = 'any_backup_created';
		$any_backup_prompt['slides']['start']['text'] = __( 'It looks like you\'ve created 10 backups with the BoldGrid Backup Plugin!', 'boldgrid-backup' ) . ' ' . $lang['feel_good_value'];

		$this->prompts = array(
			$title_description_prompt,
			$download_prompt,
			$any_backup_prompt,
		);

		$this->ratingPrompt->savePrompts( $this->prompts );

		$this->existingPrompt = $any_backup_prompt;

		$this->newPrompt                            = $default_prompt;
		$this->newPrompt['name']                    = 'new_prompt';
		$this->newPrompt['slides']['start']['text'] = 'Start slide text';
	}

	public function setup() {
		$this->ratingPrompt = new \Boldgrid\Library\Library\RatingPrompt();

		$this->reset();
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
	public function testAddPrompt() {
		// Reset our prompts.
		update_option( 'bglib_rating_prompt', $this->prompts );

		// Add a new prompt.
		$added = $this->ratingPrompt->addPrompt( $this->newPrompt );
		$this->assertTrue( $added );

		// Add a prompt that already exists.
		$added = $this->ratingPrompt->addPrompt( $this->existingPrompt );
		$this->assertFalse( $added );
	}

	/**
	 * Add admin notices.
	 *
	 * @since 2.7.7
	 */
	public function testAdminNotices() {
		// NOT TESTED.
	}

	/**
	 * Dismiss a rating prompt via ajax.
	 *
	 * @since 2.7.7
	 *
	 * @hook wp_ajax_blib_rating_prompt_dismiss
	 */
	public function testAjaxDismiss() {
		// NOT TESTED.
	}

	/**
	 * Get an array of attributes for a decision's <a> tag.
	 *
	 * @since 2.7.7
	 *
	 * @param  array $decision A decision from a slide.
	 * @return array
	 */
	public function testGetDecisionAttributes() {
		/*
		 * [data-action] => dismiss
		 * [href] => https://wordpress.org/support/plugin/boldgrid-backup/reviews/
		 * [target] => _blank
		 * [data-next-slide] => thanks
		 */
		$attributes = $this->ratingPrompt->getDecisionAttributes( $this->prompts[0]['slides']['start']['decisions']['sure_will'] );
		$this->assertEquals( 4, count( $attributes ) );
		$this->assertEquals( 'dismiss', $attributes['data-action'] );
		$this->assertEquals( $attributes['href'], $this->prompts[0]['slides']['start']['decisions']['sure_will']['link'] );
		$this->assertEquals( '_blank', $attributes['target'] );
		$this->assertEquals( $attributes['data-next-slide'], $this->prompts[0]['slides']['start']['decisions']['sure_will']['slide'] );

		/*
		 * [data-action] => snooze
		 * [href] =>
		 * [data-snooze] => 604800
		 * [data-next-slide] => maybe_later
		 */
		$attributes = $this->ratingPrompt->getDecisionAttributes( $this->prompts[0]['slides']['start']['decisions']['maybe_still_testing'] );
		$this->assertEquals( 4, count( $attributes ) );
		$this->assertEquals( 'snooze', $attributes['data-action'] );
		$this->assertEquals( '', $attributes['href'] );
		$this->assertEquals( $attributes['data-snooze'], $this->prompts[0]['slides']['start']['decisions']['maybe_still_testing']['snooze'] );
		$this->assertEquals( $attributes['data-next-slide'], $this->prompts[0]['slides']['start']['decisions']['maybe_still_testing']['slide'] );

		/*
		 * [data-action] => dismiss
		 * [href] =>
		 * [data-next-slide] => already_did
		 */
		$attributes = $this->ratingPrompt->getDecisionAttributes( $this->prompts[0]['slides']['start']['decisions']['already_did'] );
		$this->assertEquals( 3, count( $attributes ) );
		$this->assertEquals( 'dismiss', $attributes['data-action'] );
		$this->assertEquals( '', $attributes['href'] );
		$this->assertEquals( $attributes['data-next-slide'], $this->prompts[0]['slides']['start']['decisions']['already_did']['slide'] );
	}

	/**
	 * Get the time of the last dismissal or snooze, whichever is latest.
	 *
	 * One reason this is used is to ensure prompts don't show one after another for the user. For
	 * example, if the user just dismissed a rating prompt, we may not want to show them another
	 * prompt for at least 2 days.
	 *
	 * @since 2.7.7
	 *
	 * @return int
	 */
	public function testGetLastDismissal() {
		// Test based on time_dismissed only.
		$this->reset();

		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[0]['time_dismissed'] = 2;
		$prompts[1]['time_dismissed'] = 1;
		$prompts[3]['time_dismissed'] = 3;
		$this->ratingPrompt->savePrompts( $prompts );

		$lastDismissed = $this->ratingPrompt->getLastDismissal();
		$this->assertEquals( 3, $lastDismissed );

		// Test using time_snoozed too.
		$this->reset();

		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[0]['time_dismissed'] = 1;
		$prompts[1]['time_snoozed'] = 3;
		$prompts[3]['time_dismissed'] = 2;
		$this->ratingPrompt->savePrompts( $prompts );

		$lastDismissed = $this->ratingPrompt->getLastDismissal();
		$this->assertEquals( 3, $lastDismissed );
	}

	/**
	 * Admin enqueue scripts.
	 *
	 * @since 2.7.7
	 */
	public function testAdminEnqueueScripts() {
		// NOT TESTED.
	}

	/**
	 * Return the markup of a prompt's slides.
	 *
	 * @since 2.7.7
	 *
	 * @param  array  $prompt A prompt.
	 * @return string
	 */
	public function testGetPromptSlides() {
		// NOT TESTED.
	}

	/**
	 * Get the markup for an individual slide.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $slide_id The id of a slide.
	 * @param  array  $slide    A array of slide configs.
	 * @return string
	 */
	public function testGetSlideMarkup() {
		// NOT TESTED.
	}

	/**
	 * Whether or not the minimum amount of time between showing prompts has been reached.
	 *
	 * @since 2.7.7
	 *
	 * @see self::getLastDismissal().
	 *
	 * @return bool
	 */
	public function testIsMinInterval() {
		// return time() > $this->getLastDismissal() + $this->minInterval;

		// With nothing dismissed, should be true.
		$this->reset();

		$this->assertTrue( $this->ratingPrompt->isMinInterval() );

		// Something dismissed in the future, not enough time will have passed to display the next.
		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[2]['time_dismissed'] = '99999999999999999999';
		$this->ratingPrompt->savePrompts( $prompts );

		$this->assertFalse( $this->ratingPrompt->isMinInterval() );

		// Set dismissal to ( minInterval + 60 seconds ) ago.
		$this->reset();
		$prompts = $this->ratingPrompt->getPrompts();
		// I dismissed this rating 2 days and 60 seconds ago.
		$prompts[2]['time_dismissed'] = time() - $this->ratingPrompt->getMinInterval() - 60;
		$this->ratingPrompt->savePrompts( $prompts );

		$this->assertTrue( $this->ratingPrompt->isMinInterval() );

		// Set dismissal to ( minInterval - 60 seconds ) ago.
		$this->reset();
		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[2]['time_dismissed'] = time() + $this->ratingPrompt->getMinInterval() + 60;
		$this->ratingPrompt->savePrompts( $prompts );

		$this->assertFalse( $this->ratingPrompt->isMinInterval() );
	}

	/**
	 * Get a prompt by name.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $name
	 * @return array
	 */
	public function testGetPrompt() {
		$this->reset();

		$prompt = $this->ratingPrompt->getPrompt( 'update_title_description' );

		$this->assertEquals( $prompt, $this->prompts[0] );
	}

	/**
	 * Get all rating prompts.
	 *
	 * @since 2.7.7
	 *
	 * @return array
	 */
	public function testGetPrompts() {
		$this->reset();

		$prompts = $this->ratingPrompt->getPrompts();

		$this->assertEquals( $prompts, $this->prompts );
	}

	/**
	 * Determine whether or not a given plugin has any dismissed notices.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $plugin A plugin name.
	 * @return bool
	 */
	public function testIsPluginDismissed() {
		// By default, nothing is dismissed.
		$this->reset();

		$this->assertFalse( $this->ratingPrompt->isPluginDismissed( 'boldgrid-backup' ) );

		// Have only 1 dismissed prompt, but it's for a different plugin.
		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[0]['plugin'] = 'some-plugin';
		$prompts[0]['time_dismissed'] = 5;
		$this->ratingPrompt->savePrompts( $prompts );

		$this->assertFalse( $this->ratingPrompt->isPluginDismissed( 'boldgrid-backup' ) );

		// Have 1 dismissed prompt, for the plugin in question.
		$this->reset();
		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[0]['time_dismissed'] = 5;
		$this->ratingPrompt->savePrompts( $prompts );

		$this->assertTrue( $this->ratingPrompt->isPluginDismissed( 'boldgrid-backup' ) );
	}

	/**
	 * Whether or not a prompt (by prompt name) exists.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $name A prompt name.
	 * @return bool
	 */
	public function testIsPrompt() {
		$this->reset();

		$this->assertTrue( $this->ratingPrompt->isPrompt( 'download_to_local_machine' ) );

		$this->assertFalse( $this->ratingPrompt->isPrompt( 'this-does-not-exist' ) );
	}

	/**
	 * Get all prompts for a plugin.
	 *
	 * @since 2.7.7
	 *
	 * @param  string $plugin The name of a plugin.
	 * @return array
	 */
	public function testGetPluginPrompts() {
		$this->reset();

		$plugin = 'my-cool-plugin';

		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[2]['plugin'] = $plugin;
		$this->ratingPrompt->savePrompts( $prompts );

		$pluginPrompts = $this->ratingPrompt->getPluginPrompts( $plugin );
		$this->assertEquals( $prompts[2], $pluginPrompts[0] );
	}

	/**
	 * Get the next prompt to display.
	 *
	 * We only display one prompt at a time. Get that prompt.
	 *
	 * @since 2.7.7
	 */
	public function testGetNext() {
		// Empty array when no next prompt.
		update_option( 'bglib_rating_prompt', array() );

		$next = $this->ratingPrompt->getNext();
		$this->assertEquals( array(), $next );

		// Basic, only looking at time dismissed.
		$this->reset();
		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[0]['time_dismissed'] = 5;
		$prompts[2]['time_dismissed'] = 10;
		$this->ratingPrompt->savePrompts( $prompts );
		$prompts = $this->ratingPrompt->getPrompts();

		$next = $this->ratingPrompt->getNext();
		$this->assertEquals( $prompts[1]['name'], $next['name'] );

		// Looking at time_dismissed and snoozed.
		$this->reset();
		$prompts = $this->ratingPrompt->getPrompts();
		$prompts[0]['time_snoozed_until'] = 6;
		$prompts[0]['time_dismissed'] = 10;
		$prompts[1]['time_snoozed_until'] = 9999999999999;
		$prompts[2]['time_snoozed_until'] = 12;
		$this->ratingPrompt->savePrompts( $prompts );
		$prompts = $this->ratingPrompt->getPrompts();

		$next = $this->ratingPrompt->getNext();
		$this->assertEquals( $prompts[2]['name'], $next['name'] );
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
	public function testUpdatePrompt() {
		$this->reset();

		$pet = 'fish';

		// Update the prompt.
		$updatedPrompt = $this->prompts[0];
		$updatedPrompt['pet'] = $pet;
		$this->ratingPrompt->updatePrompt( $updatedPrompt );

		// Make sure it updated.
		$prompts = $this->ratingPrompt->getPrompts();
		$this->assertEquals( $pet, $prompts[0]['pet'] );
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
	public function testSavePrompts() {
		$this->reset();

		$newPrompts = array( 1, 2, 3 );

		$this->ratingPrompt->savePrompts( $newPrompts );

		$prompts = $this->ratingPrompt->getPrompts();

		$this->assertEquals( $newPrompts, $prompts );
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
	public function testUpdatePromptKey() {
		$this->reset();

		$name =  'update_title_description';
		$key  =  'fish';
		$value = 'catfish';

		$this->ratingPrompt->updatePromptKey( $name, $key, $value );

		$prompt = $this->ratingPrompt->getPrompt( $name );

		$this->assertEquals( $value, $prompt[$key] );
	}
}
