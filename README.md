# library

The BoldGrid Library for shared code used in official BoldGrid plugins and themes.

Using composer, you can get started quickly:

```php
composer require boldgrid/library

```

## Changelog ##

### 2.2.1 In progress ###
* Update:  JIRA WPB-3725    Use a transient in Checker::findUpdated().
* Bug fix: JIRA WPB-3724    Do not call getLicense if Connect Key is not available.
* Update:  JIRA WPB-3721    Moved Plugin\Checker back to Library.
* Bug fix:                  Duplicate admin notices showing.

### 2.2.0 ###
* Bug fix: JIRA WPB-3714    Fixed PHP notice in Key::verifyData().
* Feature:                  As a user, I can refresh my license key status.

### 2.1.0 ###
* Feature: JIRA BGTHEME-103 Added ClaimPremiumKey notice.

### 2.0.0 ###
* Feature: JIRA BGINSP-3    Added filter to display Connect Key prompt admin notice, even if dismissed.
* Update:  JIRA WPB-3684    Moved plugin install to its own package (boldgrid/plugin-install).
* Feature: JIRA BGBKUP-75   Added dismiss/undismiss for Connect Key prompt/notice.

### 1.1.6 ###
* Bug fix: JIRA BGBKUP-67   Fixed key prompt is-dismissible, and hid duplicate notice from other plugin.
* Feature: JIRA WPB-3638    Added post-and-page-builder and boldgrid-easy-seo to the Plugins >> Add New page.
* Bug fix: JIRA WPB-3636    Fixed invalid version number sent for plugins not installed, but in config.
* Bug fix: JIRA WPB-3635    API calls now respect release channels.

### 1.1.5 ###
* Bug fix: JIRA WPB-3518    Fixed fatal error in certain scenarios from double inclusion of WP core files.

### 1.1.4 ###
* Bug fix: JIRA WPB-3427    Adjusted handling of plugin update transients.
*                           Added premium product check to license class.

### 1.1.3 ###
*                           Validate plugin before printing card.

### 1.1.2 ###
*                           Bug fixes.

### 1.1.1 ###
*                           Added form affiliate data.

### 1.1.0 ###
*                           Added action for when theme release channel changed.
*                           Added Reseller class.
