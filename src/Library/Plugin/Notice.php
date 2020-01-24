<?php
/**
 * BoldGrid Library Plugin Page Notice.
 *
 * @package Boldgrid\Plugin
 *
 * @since SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Library\Library\Plugin;

/**
 * Notice class for Plugin\Page.
 *
 * This class is a specific Notice
 * used by the Boldgrid\Library\Library\Plugin\Page 
 * and Boldgrid\Library\Library\Plugin\Plugin classes.
 *
 * @since SINCEVERSION
 */
class Notice {

	/**
	 * Notice ID
	 *
	 * @since SINCEVERSION
	 * @var string
	 * @access protected
	 */
	protected $id;

	/**
	 * Notice Page slug.
	 *
	 * @since SINCEVERSION
	 * @var string
	 * @access protected
	 */
	protected $pageSlug;

	/**
	 * Notice Version
	 *
	 * Version of plugin this notice was added on.
	 *
	 * @since SINCEVERSION
	 * @var string
	 * @access protected
	 */
	protected $version;

	/**
	 * Plugin Object
	 *
	 * Plugin Object this belongs to.
	 *
	 * @since SINCEVERSION
	 * @var Plugin
	 * @access protected
	 */
	protected $plugin;

	/**
	 * Notice Is Unread
	 *
	 * Specifies if the Notice is Unread or not.
	 *
	 * @since SINCEVERSION
	 * @var bool
	 * @access protected
	 */
	protected $isUnread;

	/**
	 * Constructor 
	 *
	 *
	 * @since SINCEVERSION
	 *
	 * @param Plugin $plugin Plugin instance that this Notice belongs to.
	 * @param array $notice {
	 *     An array of notice values.
	 *
	 *     @type string $id notice ID.
	 *     @type string $page slug of the page this notice is on.
	 *     @type string $version version of plugin this notice was released on.
	 * }
	 */
	public function __construct( Plugin $plugin, array $notice ) {
		if ( $this->alreadyExists( $notice['id'] ) ) {
			$originalNotice = $this->getFromOptions( $notice['id'] );
			$this->id       = $originalNotice->id;
			$this->pageSlug = $originalNotice->pageSlug;
			$this->plugin   = $originalNotice->plugin;
			$this->noticeVersionChanged( $originalNotice, $notice );
			$this->updateNoticeOption( $this );
		} else {
			$this->id        = $notice['id'];
			$this->pageSlug  = $notice['page'];
			$this->version   = $notice['version'];
			$this->isUnread  = true;
			$this->plugin    = $plugin;
		}
	}

	/**
	 * Get notice ID.
	 *
	 * @since SINCEVERSION
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set notice ID.
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $id Notice ID.
	 * @access private
	 */
	private function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * Get slug of the page this feature is on.
	 *
	 * @since SINCEVERSION
	 *
	 * @return string
	 */
	public function getPageSlug() {
		return $this->pageSlug;
	}

	/**
	 * Sets the pageSlug.
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $pageSlug Notice Page.
	 */
	public function setPageSlug( string $pageSlug ) {
		$this->pageSlug = $pageSlug;
	}

	/**
	 * Get version of plugin this notice was added on.
	 *
	 * @since SINCEVERSION
	 *
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Set version of plugin this notice was added on.
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $version Version of plugin this notice was added on.
	 */
	public function setVersion( string $version ) {
		$this->version = $version;
	}

	/**
	 * Maybe the notice count should show.
	 *
	 * Returns true if this plugin's version number is greater than the first installed version,
	 * and feature's version number is greater than or equal to current version.
	 *
	 * @since SINCEVERSION
	 *
	 * @return bool
	 */
	public function maybeShow() {
		$pluginVersion     = $this->plugin->getPluginData()['Version'];
		$versionIsNotFirst = $this->plugin->firstVersionCompare( $pluginVersion, '<' );
		$featureIsNewer    = version_compare( $this->version, $pluginVersion , '>=' );
		return ( $featureIsNewer && $versionIsNotFirst);
	}

	/**
	 * Get isUnread Value.
	 *
	 * @since SINCEVERSION
	 *
	 * @return bool
	 */
	public function getIsUnread() {
		if ( $this->maybeShow() ) {
			return $this->isUnread;
		}
		return false;
	}

	/**
	 * Set the notice as read or unread.
	 *
	 * @since SINCEVERSION
	 *
	 * @param bool $isUnread Specifies if the Notice is Unread or not.
	 */
	public function setIsUnread( $isUnread ) {
		$this->isUnread = $isUnread;
		$this->updateNoticeOption();
	}
	
	/**
	 * Determine if Notice Exists in Options Table.
	 *
	 * @since SINCEVERSION
	 *
	 * @return bool true if noticeId exists.
	 */
	public function alreadyExists( $noticeId ) {
		return ! empty( $this->getFromOptions( $noticeId ) );
	}

	/**
	 * Get Notice instance from wp_options row.
	 *
	 * @since SINCEVERSION
	 *
	 * @param string $noticeId
	 * @return Notice
	 */
	private function getFromOptions( $noticeId ) {
		$option = get_option( 'boldgrid_plugin_page_notices', [] );
		foreach ( $option as $notice ) {
			if ( $notice->id == $noticeId ) {
				return $notice;
			}
		}
	}
	
	/**
	 * Update Notice Option.
	 *
	 * Updates option row in wp_options table. If The NoticeId 
	 * already exists, then it's object is replaced with $this.
	 * Otherwise, $this is appended to the array.
	 *
	 * @since SINCEVERSION
	 */
	public function updateNoticeOption() {
		$option = get_option( 'boldgrid_plugin_page_notices', [] );
		if ( $this->alreadyExists( $this->id ) ) {
			$originalNotice = getFromOptions( $this->id );
			$option[ array_search($originalNotice, $option) ] = $this;
		} else {
			$option[] = $this;
		}
		update_option( 'boldgrid_plugin_page_notices' , $option);
	}

	/**
	 * Get Plugin.
	 *
	 * Get plugin instance that this notice belongs to
	 *
	 * @since SINCEVERSION
	 *
	 * @return Plugin
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * Set Plugin.
	 *
	 * Set plugin Instance that this notice belongs to
	 *
	 * @since SINCEVERSION
	 *
	 * @param Plugin $plugin Plugin Object this belongs to.
	 */
	public function setPlugin( Plugin $plugin ) {
		$this->plugin = $plugin;
	}
	
	/**
	 * Notice Version Changed.
	 *
	 * Determines if an existing Notice's version number has changed
	 * since it was placed in options table. If it has changed, then it marks
	 * the notice unread again. This will help bring attention to old notices 
	 * that may have been revised.
	 *
	 * @since SINCEVERSION
	 *
	 * @param Notice $originalNotice
	 * @param array $newNotice
	 */

	private function noticeVersionChanged( Notice $originalNotice, array $newNotice ) {
		if ( version_compare( $originalNotice->version, $newNotice['version'], '!=' ) ) {
			$this->version   = $newNotice['version'];
			$this->isUnread = true;
		}
		else {
			$this->version = $originalNotice->getVersion();
			$this->isUnread = $originalNotice->isUnread;
		}
	}
}