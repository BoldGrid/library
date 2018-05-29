# library

The BoldGrid Library for shared code used in official BoldGrid plugins and themes.

Using composer, you can get started quickly:

```php
composer require boldgrid/library

```

## Changelog ##

### 2.3.5 ###
* Bug fix:                  Updating boldgrid-backup link in config.

### 2.3.4 ###
* Update:  JIRA BGBKUP-220  Sanitize inputs.

### 2.3.3 ###
* Bug fix: JIRA BGINSP-15   Disable Connect Key request button after submission.

### 2.3.2 ###
* Update:                   More clear error message on failed ajax license clears.
* Update:                   Added prettier-eslint.

### 2.3.1 ###
* Bug fix: JIRA BGBKUP-180  Fixed empty check for PHP 5.3.

### 2.3.0 ###
* Feature: JIRA BGBKUP-180  Handle auto updates as configured by the boldgrid_settings option.

### 2.2.2 ###
* Bug fix: JIRA WPB-3767    Prevent invalid API calls for check-version.

### 2.2.1 ###
* Bug fix: JIRA WPB-3730    Fixed loading of plugin installer class.
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
