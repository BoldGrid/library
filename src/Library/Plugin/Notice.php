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
     * @var string
     * @access protected
     * @since SINCEVERSION
     */
    protected $id;

    /**
     * Notice Page slug
     * 
     * @var string
     * @access protected
     * @since SINCEVERSION
     */
    protected $pageSlug;

    /**
     * Notice Version
     * 
     * Version of plugin this notice was added on
     * 
     * @var string
     * @access protected
     * @since SINCEVERSION
     */
    protected $version;

    /**
     * Plugin Object
     * 
     * Plugin Object this belongs to
     * 
     * @var Plugin
     * @access protected
     * @since SINCEVERSION
     */
    protected $plugin;

    /**
     * Notice Is Unread
     * 
     * Specifies if the Notice is Unread or not
     * 
     * @var bool
     * @access protected
     * @since SINCEVERSION
     */
    protected $isUnread;

    /**
     * Constructor 
     * 
     * @param Plugin $plugin Plugin instance that this Notice belongs to
     * @param array $notice An array of Notice data from plugin config
     * @since SINCEVERSION
     * 
     */
    public function __construct( Plugin $plugin, array $notice ) {
        if ( false !== $this->alreadyExists( $notice['id'] ) ) {
            $originalNotice = $this->getNoticeFromOptions( $notice['id'] );
            $this->id       = $originalNotice->id;
            $this->pageSlug = $originalNotice->pageSlug;
            $this->plugin   = $originalNotice->plugin;
            if ( version_compare( $originalNotice->version, $notice['version'], '!=' ) ) {
                $this->version   = $notice['version'];
                $this->isUnread = true;
            }
            else {
                $this->version = $originalNotice->getVersion();
                $this->isUnread = $originalNotice->isUnread;
            }
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
	 * Get notice ID
	 *
	 * @return string
	 * @since SINCEVERSION
	 */
    public function getId() {
        return $this->id;
    }

	/**
	 * Set notice ID
	 *
	 * @param string $id Notice ID.
     * @access private
	 * @since SINCEVERSION
	 *
	 */
	private function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * Get slug of the page this feature is on.
	 *
	 * @return string
	 * @since SINCEVERSION
	 */
    public function getPageSlug() {
        return $this->pageSlug;
    }

	/**
	 * Set pageSlug
	 *
	 * @param string $pageSlug Notice Page.
	 * @since SINCEVERSION
	 *
	 */
	public function setPageSlug( string $pageSlug ) {
		$this->pageSlug = $pageSlug;
	}

	/**
	 * Get version of plugin this notice was added on
	 *
	 * @return string
	 * @since SINCEVERSION
	 */
    public function getVersion() {
        return $this->version;
    }

	/**
	 * Set version of plugin this notice was added on
	 *
	 * @param string $version Version of plugin this notice was added on.
	 * @since SINCEVERSION
	 *
	 */
	public function setVersion( string $version ) {
		$this->version = $version;
	}

    /**
     * Maybe Show
     * 
     * Returns true if this plugin's version number is
     * greater than the first installed version, and feature's version number is 
     * greater than or equal to current version
     * @return bool
     * @since SINCEVERSION
     */
    
    public function maybeShow() {
        $pluginVersion = $this->plugin->getPluginData()['Version'];
        $versionIsNotFirst = $this->plugin->firstVersionCompare( $pluginVersion, '<' );
        $featureIsNewer = version_compare( $this->version, $pluginVersion , '>=' );
        return ( $featureIsNewer && $versionIsNotFirst);
    }

	/**
	 * Get isUnread Value.
	 *
	 * @return bool
	 * @since SINCEVERSION
	 */
    public function getIsUnread() {
        if ( $this->maybeShow() ) {
            return $this->isUnread;
        }
        return false;
    }

	/**
	 * Set the notice as read or unread
	 *
	 * @param bool $isUnread Specifies if the Notice is Unread or not.
	 * @since SINCEVERSION
	 *
	 */
	public function setIsUnread( $isUnread ) {
        $this->isUnread = $isUnread;
        $this->updateNoticeOption();
    }
    
    /**
     * Determine if Notice Exists in Options Table
     * 
     * If the notice exists, then the return value is the index
     * of that notice in the $option array. If the notice does not exist
     * this returns false.
     * 
     * @return mixed 
     * @since SINCEVERSION
     */
    public function alreadyExists( $noticeId ) {
        $option = get_option( 'boldgrid_plugin_page_notices');
        if ( $option ) {
            foreach ( $option as $notice ) {
                if ( $notice->getId() == $noticeId ) {
                    return array_search($notice, $option);
                }
            }
        }
        return false;
    }

    /**
     * Get Notice instance from wp_options row.
     * 
     * @param string $noticeId
     * @return Notice
     * @since SINCEVERSION
     */
    private function getNoticeFromOptions( $noticeId ) {
        $option = get_option( 'boldgrid_plugin_page_notices');
        foreach ( $option as $notice ) {
            if ( $notice->id == $noticeId ) {
                return $notice;
            }
        }
    }
    
    /**
     * Update Notice Option
     * 
     * Updates option row in wp_options table. If The NoticeId 
     * already exists, then it's object is replaced with $this.
     * Otherwise, $this is appended to the array.
     * 
     * @since SINCEVERSION
     */
    public function updateNoticeOption() {
        $option = get_option( 'boldgrid_plugin_page_notices');
        if ( $option && false !== $this->alreadyExists( $this->id ) ) {
            $option[$this->alreadyExists( $this->id )] = $this;
        } else {
            $option[] = $this;
        }
        update_option( 'boldgrid_plugin_page_notices' , $option);
    }

	/**
     * Get Plugin
     * 
	 * Get plugin instance that this notice belongs to
	 *
	 * @return Plugin
	 * @since SINCEVERSION
	 */
    public function getPlugin() {
        return $this->plugin;
    }

	/**
     * Set Plugin
     * 
	 * Set plugin Instance that this notice belongs to
	 *
	 * @param Plugin $plugin Plugin Object this belongs to.
	 * @since SINCEVERSION
	 *
	 */
	public function setPlugin( Plugin $plugin ) {
		$this->plugin = $plugin;
	}
}