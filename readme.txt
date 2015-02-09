=== Gravity Forms Checkout for Easy Digital Downloads ===
Tags: easy digital downloads, edd, gravity forms, gravityforms
Requires at least: 3.3
Tested up to: 4.1
Contributors: katzwebdesign, katzwebservices
License: GPL 3 or higher

Integrate Gravity Forms purchases with Easy Digital Downloads

== Description ==

Use Gravity Forms to sell Easy Digital Downloads products. Simply add a product field to your Gravity Forms form, connect it to an EDD product, and then set your prices. Use any of your existing Gravity Forms gateways and payment methods.

For support, [visit the Help Docs](http://kws.helpscoutdocs.com/collection/29-gravity-forms-checkout-for-easy-digital-downloads).

__To translate the plugin__ into your language, please [submit your translation here](https://www.transifex.com/projects/p/edd-gravity-forms-checkout/).

== Screenshots ==


== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
1. Activate the plugin
1. For more, [visit the Help Docs](http://kws.helpscoutdocs.com/collection/29-gravity-forms-checkout-for-easy-digital-downloads).

== Frequently Asked Questions ==

== Changelog ==

= 1.3 on February 9, 2015 =
* Modified: When a product with variable pricing was purchased, the customer's purchase would include the base product and also the price option. Now, the customer's purchase will include only the price option. If you want to restore the functionality, [read the how-to here](http://kws.helpscoutdocs.com/article/219-include-base-product-download-links-when-purchasing-a-product-with-price-variations)


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