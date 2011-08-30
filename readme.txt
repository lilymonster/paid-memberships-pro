=== Paid Memberships Pro ===
Contributors: strangerstudios
Tags: memberships, ecommerce, authorize.net, paypal
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.2

An infinitely customizable Membership Plugin for WordPress integrated with Authorize.net or PayPal(r) for recurring payments, flexible content control, themed registration, checkout, and more ...


== Description ==
Paid Memberships Pro is a WordPress Plugin and support community for membership site curators. PMPro's rich feature set allows you to add a new revenue source to your new or current blog or website and is flexible enough to fit the needs of almost all online and offline businesses.

== Installation ==

1. Upload the `paid-memberships-pro` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Follow the instructions here to setup your memberships: http://www.paidmembershipspro.com/support/initial-plugin-setup/

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the WordPress support forum and we'll fix it right away. Thanks for helping. http://wordpress.org/tags/paid-memberships-pro?forum_id=10

= I need help installing, configuring, or customizing the plugin. =

Please visit our premium support site at http://www.paidmembershipspro.com for more documentation and our support forums.

== Screenshots ==

1. Paid Memberships Pro supports multiple membership levels.
2. On-site checkout via Authorize.net or PayPal Website Payments Pro. (Off-site checkout coming soon.)
3. Use Discount Codes to offer access at lower prices for special customers.

== Changelog ==
= 1.2 =
* Fixing some wonkiness with the 1.1.15 update.
* Fixed "warning" showing up on discount code pages.
* Tweaked the admin pages a bit for consistency.
* Added screenshots and FAQ to the readme.
* Figured we were due for a bigger version step.

= 1.1.15 =
* Discount Codes Added!
* Removed some redundant files that slipped into the services folder.
* Fixed the !!levels!! variable for message settings of the advanced tab.
* Changing some ids columns in tables to unsigned.

= 1.1.14 =
* Now encoding #'s when sending info via Authorize.net's API. This may prevent some address conflicts.

= 1.1.13 =
* No longer adding "besecure" custom field to the billing and checkout pages. You can still add this manually to selectively require SSL on a page. If you are trying to do a free membership without SSL, you will have to make sure the besecure custom field is deleted from the Membership-Checkout page, especially if you are upgrading from an older version of PMPro.
* Added a filter before sending the default WP welcome notification email. Return false for the "pmpro_wp_new_user_notification" hook/filter to skip sending the WP default welcome email (because in many cases they are already getting an email from PMPro as well).

= 1.1.12 =
* Revenue report on members list page. (Rought estimate only that doesn't take into account trial periods and billing limits.)
* Enabling weekly recurring payments for Authorize.net by converting week period to 7 days * # months.
* Improved error handling on checkout page.
* Now running "pmpro_after_change_membership_level" actions after the "pmpro_after_checkout" action. Previously this hook was only called when a membership level was changed via the WP admin.		
* Won't complain about setting up a Payment Gateway if you only have free membership levels.
* The "besecure" custom field is not added to the billing or checkout by default anymore when you run the "create the pages for me" option in the settings. Whether or not to use HTTPS on a page is now handled in the preheader files for each page (see below).
* The plugin won't force SSL on the checkout page anymore unless the membership level requires payment. If your checkout page is still running over HTTPS/SSL for free membership checkouts, make sure the "besecure" custom field has been deleted on your checkout page. You can use the "besecure" custom field or the "pmpro_besecure" filter to override the plugin's decision.
* The plugin won't force SSL on the cancel page anymore. Again, you can override this using the "besecure" custom field or the "pmpro_besecure" filter.
							
= 1.1.11 =
* Removed some debug code from the invoice page that might have shown on error.
* Added check to recaptcha library code incase it is already installed. (Let's hope other plugin developers are doing the same.)
* Removed the TinyMCE editor from the description field on the edit membership level page. It was a little buggy. Might bring it back later.

= 1.1.10 =
* added a hook/filter "pmpro_rss_text_filter"
* added a hook/filter "pmpro_non_member_text_filter"
* added a hook/filter "pmpro_not_logged_in_text_filter"
* adjusted the pmpro_has_membership_access() function
* added a hook/filter "pmpro_has_membership_access_filter"
* updated the hook/filter "pmpro_has_membership_access_filter_{post-type}"
* removed the "pmpro_has_membership_access_action_{post-type}" hook/action
* update invoice page to handle case where no invoice is found

= 1.1.9 =
* You can now set individual posts to require membership without assigning them to a category.
* Fixed bug with the confirmation email during signup.
* Fixed a CSS bug on the cancel membership page.

= 1.1.8 =
* Fix for login/registration URL rerouting.
* Added members list to admin bar menu.
* Added warning/error when trying to checkout before the payment gateway is setup.
* Fixed some error handling in the order class.
* Fixed a bug that occurred when processing amounts less than $1.

= 1.1.7 =
* Fixed bugs with http to https redirects and visa versa.
* Fixed redirect bugs for sites installed in a subdomain.

= 1.1.6 =
* Fixed MySQL bug showing up on some users add membership level page.

= 1.1.5 =
* Required fix for PayPal Website Payments Pro processing. Please update.
* Fixed bug with pagination on members list.
* Fixed bugs with errors thrown by MemberOrder class.
* Updated login/registration URL rerouting.

= 1.1.4 =
* Custom Post Types default to allowing access
* Fixed login_redirect code.
* Added pmpro_login_redirect filter for when members login.

= 1.1.3 =
* Getting ready for the WP plugin repository
* License text update.

= 1.1.2 =
* Added hooks to checkout page for customizing registration fields.
* Fixed bug in pmpro_getLevelCost();
* Another CCV/CVV fix for Authorize.net.
* License text update.
* Admin notices are loaded via Ajax now.

= 1.1.1 =
* Added honeypot to signup page.
* Updated pmpro_add_pages to use capabilities instead of user levels
* Fixed checkboxes in admin screens.
* Now checking that passwords match on signup.
* Properly sending CCV/CVV codes to Authorize.net.

= 1.0 =
* This is the launch version. No changes yet.
