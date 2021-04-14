=== Gravity Forms Checkout for Easy Digital Downloads ===
Tags: easy digital downloads, edd, gravity forms, gravityforms
Requires at least: 4.5
Tested up to: 5.7
Contributors: easydigitaldownloads, katzwebservices
License: GPL 3 or higher

Integrate Gravity Forms purchases with Easy Digital Downloads

== Description ==

Use Gravity Forms to sell Easy Digital Downloads products. Simply add a product field to your Gravity Forms form, connect it to an EDD product, and then set your prices. Use any of your existing Gravity Forms gateways and payment methods.

__To translate the plugin__ into your language, please [submit your translation here](https://www.transifex.com/projects/p/edd-gravity-forms-checkout/).

== Screenshots ==


== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
1. Activate the plugin
1. For more, [visit the Help Docs](https://easydigitaldownloads.com/support/).

== Frequently Asked Questions ==

== Changelog ==


= 1.5.2 on October 18, 2019 =
* Fix: Change GF integration hook priority to 49 to fix Stripe Checkout purchases.

= 1.5 =

* Added: Gravity Forms Transaction ID now stored in EDD
* Added: Customer address now stored
* Fixed: Restored [`edd_gf_use_details_from_logged_in_user`](https://docs.easydigitaldownloads.com/article/1971-how-to-use-the-submitted-form-data-instead-of-the-current-logged-in-user-data) filter

__Developer Notes:__

* Deprecated `r()` logging method
* Added unit testing

= 1.4.2 on June 23, 2016 =
* Compatible with Easy Digital Downloads 2.6 and Gravity Forms 2.0
* Fixed: Purchase quantity is now properly handled; a download is added for each defined quantity
* Fixed: Empty payments being created that have no customer details
* Fixed: Removed call to deprecated `get_currentuserinfo()` function

= 1.4.1 on February 17, 2016 =
* Update the "EDD Price ID" label to be more accurately called "EDD Price ID or Name"

= 1.4 on January 27, 2016 =
* Added: Support for redirecting to EDD Purchase Confirmation page [Read how to set this up](https://docs.easydigitaldownloads.com/article/1970-how-to-redirect-to-the-edd-payment-confirmation-page)
* Fixed: Prevent duplicate payment records
* Fixed: Default Price Option wasn't set when loading choices
* Fixed: Broken links to documentation
* Tweak: If `KWS_GF_EDD::debug` is set to true, print Javascript console logs

= 1.3.1 on April 7, 2015 =
* Fixed: Issues where Gravity Forms entry and EDD purchase date differ
* Fixed: Display nothing in the Product field when EDD isn't active
* Fixed: If a Variable Product doesn't have any options configured in Gravity Forms, use the first price option as default.

= 1.3 on February 9, 2015 =
* Modified: When a product with variable pricing was purchased, the customer's purchase would include the base product and also the price option. Now, the customer's purchase will include only the price option. If you want to restore the functionality, [read the how-to here](http://kws.helpscoutdocs.com/article/219-include-base-product-download-links-when-purchasing-a-product-with-price-variations)
* Fixed: Plugin affecting Conditional Logic for non-product fields

= 1.2.3 on January 13, 2015 =
* Fixed: Mark free purchases as Complete
* Confirmed WordPress 4.1 compatibility

= 1.2.2 on December 2 =
* Fixed: Support for legacy Gravity Forms payment Addons
* Fixed: PHP warnings when Easy Digital Downloads is not active
* Added: Additional logging for use with Gravity Forms Logging Tool

= 1.2.1 on November 14 =
* Fixed: Updated payments when payment status is updated in Gravity Forms

= 1.2 on October 13 =
* Fixed: issue where plugin was overriding existing options for options fields that were not connected to an EDD download
* Added: Support for Product fields loading EDD Variations, when using the Radio or Drop Down Field Type
* Fixed: Properly handle Product Price + Option Price, if set
Previously, the Product for an Option would always be processed as $0.00. Now, the price is used as Gravity Forms intends: the base price, with the Options fiel as modifiers to that price.
* Support multiple download purchases using Options field checkboxes
* Added: Payment status "Void" to support Gravity Forms updates
* Improved: Support for using Simple Name field
    * Fixed error on submission
    * Sets the user's Display Name to the submitted value
* Fixed: Force orders to be 0 or positive
* Fixed: Don't show EDD connection information for Pricing Fields other than Product and Option
* Tweak: Added a minified Javascript file
* Tweak: Updated `.po` file

= 1.1 on September 2 =
* Fixed support for separate Gravity Forms "Quantity" fields
* Modified how the plugin processes downloads
* Fixed fatal error on Edit Form page when EDD isn't active but the add-on is

= 1.0.5 on July 15 =
* Fixed: plugin had been resetting field options in Form Editor

= 1.0.4 on July 3 =
* Fixed scripts not loading when Gravity Forms "No-Conflict Mode" is enabled

= 1.0.3 =
* Fixed compatibility with the official Gravity Forms Stripe add-on.

= 1.0.2 =
* Added [Gravity Forms Logging Tool](http://www.gravityhelp.com/downloads/#Gravity_Forms_Logging_Tool) support

= 1.0.1 =
* Added verification that there are EDD Downloads connected to the Gravity Form on submission.

= 1.0 =

* Liftoff!

== Upgrade Notice ==

= 1.2.1 on November 14 =
* Fixed: Updated payments when payment status is updated in Gravity Forms

= 1.2 on October 13 =
* Fixed: issue where plugin was overriding existing options for options fields that were not connected to an EDD download
* Added: Support for Product fields loading EDD Variations, when using the Radio or Drop Down Field Type
* Fixed: Properly handle Product Price + Option Price, if set
Previously, the Product for an Option would always be processed as $0.00. Now, the price is used as Gravity Forms intends: the base price, with the Options fiel as modifiers to that price.
* Support multiple download purchases using Options field checkboxes
* Added: Payment status "Void" to support Gravity Forms updates
* Improved: Support for using Simple Name field
    * Fixed error on submission
    * Sets the user's Display Name to the submitted value
* Fixed: Force orders to be 0 or positive
* Fixed: Don't show EDD connection information for Pricing Fields other than Product and Option
* Tweak: Added a minified Javascript file
* Tweak: Updated `.po` file

= 1.1 on September 2 =
* Fixed support for separate Gravity Forms "Quantity" fields
* Modified how the plugin processes downloads
* Fixed fatal error on Edit Form page when EDD isn't active but the add-on is

= 1.0.5 on July 15 =
* Fixed: plugin had been resetting field options in Form Editor

= 1.0.4 on July 3 =
* Fixed scripts not loading when Gravity Forms "No-Conflict Mode" is enabled

= 1.0.3 =
* Fixed compatibility with the official Gravity Forms Stripe add-on.

= 1.0.2 =
* Added [Gravity Forms Logging Tool](http://www.gravityhelp.com/downloads/#Gravity_Forms_Logging_Tool) support

= 1.0.1 =
* Added verification that there are EDD Downloads connected to the Gravity Form on submission.

= 1.0 =

* Liftoff!
