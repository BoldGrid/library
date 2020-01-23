<?php
/**
 * BoldGrid Library Plugin Page.
 *
 * @package Boldgrid\Plugin
 *
 * @since SINCEVERSION
 * 
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
     * @since SINCEVERSION
     * 
     * @var Plugin
     * @access protected
     */
    protected $plugin;

    /**
     * Page Slug
     * 
     * @since SINCEVERSION
     * 
     * @var string
     * @access protected
     */
    protected $slug;
    
    /**
     * Plugin Config
     * 
     * The Config array passed to this page.
     * 
     * @since SINCEVERSION
     * 
     * @var array
     */
    protected $pluginConfig = [];

    /**
     * Page Notice
     * 
     * An array of Notices for this page.
     * 
     * @since SINCEVERSION
     * 
     * @var array
     */
    protected $notices;
    
    /**
	 * Constructor.
	 *
     * @since SINCEVERSION
     * 
	 * @param Plugin $plugin object that this page belongs to.
	 * @param string $slug For example: "plugin" from plugin/plugin.php.
	 */
	public function __construct( Plugin $plugin, $slug ) {

        $this->setPlugin( $plugin );

        $this->setPluginConfig();

        $this->setNotices();

        $this->setSlug( $slug );
    }

    /**
     * Get Plugin.
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
     * @since SINCEVERSION
     */
    private function setPlugin( $plugin ) {
        $this->plugin = $plugin;
    }

	/**
	 * Get plugin Config.
	 *
     * @since SINCEVERSION
     *
	 * @return array
	 */
    public function getPluginConfig() {
        return $this->pluginConfig;
    }

	/**
	 * Set plugin Config.
	 *
	 * @since SINCEVERSION
     *
     * @param array $pluginConfig Plugin Config.
     * @access private
	 */
	private function setPluginConfig() {
        $pluginConfig = $this->getPlugin()->getPluginConfig();
		$this->pluginConfig = $pluginConfig;
	}

	/**
	 * Get an array of Notice Counts for this page.
     * 
	 * @since SINCEVERSION
     * 
	 * @return array
	 */
    public function getNotices() {
        return $this->notices;
    }

    /**
     * Get Notice by ID.
     * 
     * @since SINCEVERSION
     * 
     * @param string $id
     * @return NoticeCount
     */
    public function getNoticeById( $id ) {
        foreach ( $this->getNotices() as $notice ) {
            if ( $notice->getId() == $id ) {
                return $notice;
            }
        }
    }

	/**
	 * Set an array of Notice Counts for this page.
	 *
	 * @since SINCEVERSION
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
     * Set Page Notices to Read.
     * 
     * @since SINCEVERSION
     */
    public function setAllNoticesRead() {
        foreach ( $this->getNotices() as $notice ) {
            $notice->setIsUnread( false );
        }
    }

	/**
	 * Get Unread Count.
	 *
	 * @since SINCEVERSION
     * 
	 * @return int
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
	 * Get Unread Markup.
     * 
     * Returns UnreadCount with html markup.
	 *
	 * @since SINCEVERSION
     * 
	 * @return string
	 */
    public function getUnreadMarkup() {
		$count = $this->getUnreadCount();
		if ( $count > 0 ) {
			return '<span class="bglib-unread-notice-count">' . esc_html( $count ) . '</span>';
		} else {
			return '<span class="bglib-unread-notice-count hidden"></span>';
		}
	}

	/**
	 * Get page Slug.
	 *
	 * @since SINCEVERSION
     * 
	 * @return string
	 */
    public function getSlug() {
        return $this->slug;
    }

	/**
	 * Set page Slug.
	 *
	 * @since SINCEVERSION
     * 
	 * @param string $slug Page Slug.
	 */
	public function setSlug( string $slug ) {
		$this->slug = $slug;
	}
}