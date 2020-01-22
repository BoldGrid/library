<?php
/**
 * BoldGrid Library Plugin Page.
 *
 * @package Boldgrid\Plugin
 *
 * @since SINCEVERSION
 * @author BoldGrid <wpb@boldgrid.com>
 */

namespace Boldgrid\Library\Library\Plugin;

/**
 * Generic page class.
 *
 * This class represents a specific page used by the 
 * Boldgrid\Library\Library\Plugin\Plugin class.
 *
 * @since SINCEVERSION
 */

class Page {

    /**
     * Plugin 
     * 
     * The Boldgrid\Library\Library\Plugin\Plugin Object
     * that this page belongs to.
     * 
     * @var Plugin
     * @access protected
     */
    protected $plugin;

    /**
     * Page Slug
     * 
     * @var string
     * @access protected
     * @since SINCEVERSION
     */
    protected $slug;
    
    /**
     * Plugin Config
     * 
     * The Config array passed to this page
     * 
     * @var array
     * @since SINCEVERSION
     */
    protected $pluginConfig;

    /**
     * Page Notice
     * 
     * An array of Notices for this page
     * 
     * @var array
     * @since SINCEVERSION
     */
    protected $notices;
    
    /**
	 * Constructor.
	 *
	 * @param Plugin $plugin object that this page belongs to.
	 * @param string $slug For example: "plugin" from plugin/plugin.php
     * @since SINCEVERSION
	 */
	public function __construct( Plugin $plugin, $slug ) {

        $this->setPlugin( $plugin );

        $this->setPluginConfig();

        $this->setNotices();

        $this->setSlug( $slug );
    }

    /**
     * Get Plugin
     * 
     * @since SINCEVERSION
     */
    public function getPlugin() {
        return $this->plugin;
    }
    
    /**
     * Set Plugin
     * 
     * @since SINCEVERSION
     */
    private function setPlugin( $plugin ) {
        $this->plugin = $plugin;
    }

	/**
	 * Get plugin Config
	 *
	 * @return array
	 * @since SINCEVERSION
	 */
    public function getPluginConfig() {
        return $this->pluginConfig;
    }

	/**
	 * Set plugin Config
	 *
     * @param array $pluginConfig Plugin Config.
     * @access private
	 * @since SINCEVERSION
	 */
	private function setPluginConfig() {
        $pluginConfig = $this->getPlugin()->getPluginConfig();
		$this->pluginConfig = $pluginConfig;
	}

	/**
	 * Get an array of Notice Counts for this page
	 *
	 * @return array
	 * @since SINCEVERSION
	 */
    public function getNotices() {
        return $this->notices;
    }

    /**
     * Get Notice by ID
     * 
     * @param string $id
     * @return NoticeCount
     * @since SINCEVERSION
     */
    public function getNoticeById( $id ) {
        foreach ( $this->getNotices() as $notice ) {
            if ( $notice->getId() == $id ) {
                return $notice;
            }
        }
    }

	/**
	 * Set an array of Notice Counts for this page
	 *
	 * @since SINCEVERSION
	 *
	 */
	public function setNotices() {
        $notices = [];
        if ( isset( $this->getPluginConfig()['page-notices'] ) ) {
            foreach ( $this->getPluginConfig()['page-notices'] as $notice ) {
                $notices[] = new Notice( $this->plugin, $notice );
            }
        }
		$this->notices = $notices;
	}

    /**
     * Set Page Notices to Read
     * 
     * @since SINCEVERSION
     */
    public function setAllNoticesRead() {
        foreach ( $this->getNotices() as $notice ) {
            $notice->setIsUnread( false );
        }
    }

	/**
	 * Get Unread Count
	 *
	 * @return int
	 * @since SINCEVERSION
	 */
    public function getUnreadCount() {
        $unreadCount = 0;
        foreach ( $this->getNotices() as $notice ) {
            if ( $notice->getIsUnread() ) {
                $unreadCount++;
            }
        }
        return $unreadCount;
    }

    /**
	 * Get Unread Markup
     * 
     * Returns UnreadCount with html markup
	 *
	 * @return string
	 * @since SINCEVERSION
	 */

    public function getUnreadMarkup() {
		$count = $this->getUnreadCount();
		if ( $count > 0 ) {
			return '<span class="unread-notice-count">' . $count . '</span>';
		} else {
			return '<span class="unread-notice-count hidden"></span>';
		}
	}

	/**
	 * Get page Slug
	 *
	 * @return string
	 * @since SINCEVERSION
	 */
    public function getSlug() {
        return $this->slug;
    }

	/**
	 * Set page Slug
	 *
	 * @param string $slug Page Slug.
	 * @since SINCEVERSION
	 *
	 */
	public function setSlug( string $slug ) {
		$this->slug = $slug;
	}
}