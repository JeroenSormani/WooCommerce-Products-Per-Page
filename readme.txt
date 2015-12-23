=== WooCommerce Products Per Page ===
Contributors: sormano
Donate link: http://jeroensormani.com/donate/
Tags: Products per page, woocommerce products, woocommerce products per page, woocommerce displayed products, woocommerce quantity products, woocommerce amount of products, woocommerce number of products, woocommerce shown products
Requires at least: 3.8.0
Tested up to: 4.4
Stable tag: 1.2.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WooCommerce Products Per Page is a easy-to-setup plugin that integrates a 'products per page' dropdown on your WooCommerce pages.

== Description ==
WooCommerce Products Per Page dropdown is easy to install and has several other product page configurations. When activated the plugin already works and has multiple settings you can set to your desire.

Options like:

- Dropdown position (top or bottom, top and bottom)
- List op options products per page to show to your visitors
- Default number of products per page
- Columns per page

**Look at the screenshots!**

### Translations ###
- Dutch
- French [(whoaloic)](http://profiles.wordpress.org/whoaloic)
- Persian [(amir_tara67)](http://profiles.wordpress.org/amir_tara67)
- Deutsch [(Michael)](http://profiles.wordpress.org/blogprofis/)
- Danish [(Keld)](https://profiles.wordpress.org/kelderkold/)
- Spanish
- Brazilian
- Russian
- Hebrew


== Installation ==

1. Upload the folder `woocommerce-products-per-page` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the settings page to fine-tune the settings if desired

== Screenshots ==

1. Dropdown box on the products page
2. Close-up of the dropdown box
3. Settings page


== Changelog ==

= 1.2.2 - 23/12/2015 =

* [i18n] - Add Russian translation
* [i18n] - Add Hebrew translation
* [Improvement] - Change deprecated function to its successor

= 1.2.1 - 05/08/2015 =

* Fix - GET method remembers query parameter strings

= 1.2.0 - 12/06/2015 =

* Improvement - Structural changes to the plugin
* NOTE - Due to the structural changes please check compatibility IF you have implemented a custom code
* Improvement - Move the settings page to WooCommerce -> Settings -> Products -> Display
* Add - Brazilian translation

= 1.1.7 - 25/04/2015 =

* Improvement - Add url escaping
* Add - Spanish translation
* Add - Danish translation

= 1.1.6 - 20/03/2015 =

* Improvement/fix - Setting the per_page query object. Preventing/fixing notices.

= 1.1.5 - 15/02/2015 =

* Fix - Console notice in rare cases after WC 2.3 update

= 1.1.4 - 10/01/2015 =

* Fix - Dropdown hides behind add to cart notice (non-ajax button)
* Add - German translation

= 1.1.3.1 - 22/12/2014 =

* Fix - Backwards compatibility fix

= 1.1.3 - 20/12/2014 =

* Improvement - Improved code quality
* Improvement - Replace $woocommerce global with WC() singleton
* Improvement - Create WooCommerce_Products_Per_Page() singleton
* Improvement - Bit clearer structure
* Remove - Return void comment tags

= 1.1.2 - 11/08/2014 =

* WC 2.2 compatibility fix

= 1.1.1 - 18/08/2014 =

* Proper translation for 'All'
* Products in admin area aren't influenced anymore
* Removed unneeded comments

= 1.1.0.1 =

* Removed - Duplicated main file with wrong name

= 1.1.0 =

* Complete plugin rewrite
* Added setting for HTTP method (defaults to POST)
* Easier use in themes/templates
* Fix - bug with product tags
* Removed stylesheet (was just one line :o)
* Inserted override for themes that would override the posts_per_page in query

= 1.0.10 =

* Fix - Notices in debug mode

= 1.0.9 =

* Improvement - For dropdown on product showing pages only
* Improvement - Compatibility for WC 2.1.X
* Fix - WC 2.1.X now saves PPP with empty cart

= 1.0.8 =

* Fix - Bug on tag archive pages
* Tweak - Dropdown only shows on pages which show products

= 1.0.7 =

* Added support for filters/orders

= 1.0.5/1.0.6 =

* Added French translation (thanks to [whoaloic](http://profiles.wordpress.org/whoaloic))
* Fixed WooCommerce 2.0.X compatibily
* Now is capable for WooCommerce 2.0.X and 2.1.X

= 1.0.4 =

* Added option to control behaviour of select*
* Added filter on option text*
* Improved coding to Wordpress coding standards

= 1.0.3 =

* Fixed dutch translation*

= 1.0.2 =

* Added Dutch translation*
* Added 'None' to the dropdown locations*
* Now uses WC sessions instead of cookies*
* Gave higher priority to hook "loop_shop_per_page"*

= 1.0.1 =

* Small update for cookies

= 1.0.0 =

* Initial release

== Upgrade Notice ==

= 1.1.4 =
Version 1.1.4 uses a different priority to position the drop down. For most people this won't affect anything, but this might be affecting your theme. Please check the drop down after updating to see if it still positioned correctly.