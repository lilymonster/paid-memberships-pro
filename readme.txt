=== Paid Memberships Pro ===
Contributors: strangerstudios
Tags: memberships, ecommerce, authorize.net, paypal
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.2.8

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
= 1.2.8 =
* Ordering levels by id (ascending) on the levels page now. Added a "pmpro_levels_array" filter that can be used to reorder the levels or alter the levels before displaying them on the levels page. The array of levels is the only parameter.
* Added expiration date to the member list and export.
* Showing a member count on the member list page.
* Added filter to change subject lines for PMPro emails. (pmpro_email_subject) The filter's first paramter is the subject, the second parameter is an object containg all of the email information. There are also filters for pmpro_email_recipient, pmpro_email_sender, pmpro_email_sender_name, pmpro_email_template, amd pmpro_email_body.
* Added an RSS feed from the PMPro blog to the dashboard.
* Now only showing the discount code field at checkout if there are discount codes in the database. Can be overriden by the pmpro_show_discount_code filter.
* Cancelling with PayPal now properly updates status to "cancelled".
* No longer trying to unsubscribe from PayPal or Authorize.net if there is no subscription ID to check against (e.g. when the user was manually added to a membership level) or if the last order does not have "success" status (e.g. they already cancelled).
* Removed PHP short tags (e.g., <?=$variable?>) for wider compatibility.

= 1.2.7 =
* Fixed bug with non-USD currencies.
* Fixed bug with phone number formatting.

= 1.2.6 =
* Fixed bug with discount codes showing up in emails, confirmation pages, and invoices.
* Added currency option to gateway settings.

= 1.2.5 =
* PayPal Express support! PayPal Express requires just a verified PayPal Business account (no monthly fees from PayPal).
* Fixed a bug when plans with a "cycle number"/billing frequency that was greater than 1 (e.g. every 4 months). Before the first payment was being scheduled 1 day/month ext out instead of e.g. 4 months out... resulting in an extra payment.
* Added some hooks to support international orders: pmpro_international_addresses, pmpro_default_country, pmpro_countries, pmpro_required_billing_fields. Example code to support international credit cards: https://gist.github.com/1212479

= 1.2.4 =
* VERY IMPORTANT BUG FIX: The getMembershipLevel function of the MemberOrder class had an error where the membership level object was not being created properly during signup and so * recurring subscriptions were not being created *. This update fixes the bug. Thanks to mvp29 for catching this.
* Fixed another bug that was causing warnings on some setups, e.g. WAMP server for Windows.
* Fixed a bug that would show warnings when visiting a login page over HTTPS.
* Fixed membership pricing wording for certain cases, e.g. every 4 months for 4 more payments.
* Fixed a bug in the email generation for order confirmations when discount codes were used. This will no longer freeze the screen.

= 1.2.3 =
* Fixed an error in the DB upgrade code that was keeping the "enddate" from being added to new members' records.

= 1.2.2 =
* Added pmpro_skip_account_fields hook. This value is used to determine if the username/password accounts fields should show up at checkout. By default, it is shown when the user is logged out and not shown when logged in. The hook allows you to return true or false to override this behavior. If the fields are skipped while no user is logged in a username and password will be automatically generated for the new user after checkout.
* You can delete discount codes now from the admin.
* Added a hook pmpro_level_cost_text to allow you to override how the cost is shown on the checkout page. Obviously don't abuse this by showing a different price than what will be charged. Be careful if you change your membership levels pricing to update your filter if needed. The hook passes the text generated by the pmpro_getLevelCost(&$level) function and also a level object which is prepopulated with levels pricing and expiration settings already adjusted for any discount codes that may be in affect.
* Added expiration settings for levels. You can set an "expiration number" and "expiration period" for any level now. e.g. "6 months" or "180 days". You can also alter expiration settings via discount codes. Expirations will be useful for offering free trials which don't require a credit card... and other scenarios you guys have come up with. A script is run once a day using WP Cron that checks for any membership that has ended and then cancels that membership. The user will lose access and the subscription setup in your payment gateway will be canceled.
* Users can "extend" a membership that is set to expire via the Membership Account page.
* Added a hook pmpro_level_expiration_text to allow you to override how the expiration information is shown on the levels and checkout pages. Again don't abuse this by showing a different expiration than is real. Be careful if you change your expiration settings to update your filter if needed. The hook passes the text generated by the pmpro_getLevelExpiration(&$level) function and also a level object which is prepopulated with levels pricing and expiration settings already adjusted for any discount codes that may be in affect.
* Added an error check if the MySQL insertion of the membership level fails. This happens after the user's credit card/etc has already been charged. The plugin tries to cancel the order just made, but might fail. The user is adviced to contact the site owner instead of trying again. I don't want to scare you. We test the checkout process a lot. So assuming that the code hasn't been tampered with and there isn't an internet outage in the microseconds between the order going through and the database being updates, you should never run into this. Still it's nice to have, just in case.
* Fixed a bug that may have caused the billing amount to show up incorrectly on the Membership Account page.
* Added the discount code used to the confirmation page, invoices, and invoice emails.
* Now sending notification emails to members 1 week before their trial period ends (if applicable). A WP cron job is setup on plugin activation. You can disable the email via the pmpro_send_trial_ending_email hook.
* Now sending notification emails to members 1 week before their membership expires (if applicable). A WP cron job is setup on plugin activation. You can disable the email via the pmpro_send_expiration_warning_email hook.
* An email is sent when a membership expires. A WP cron job is setup on plugin activation. You can disable the email via the pmpro_send_expiration_email hook.
* Note: Right now users cannot "extend" a membership that is about to expire without first canceling their current membership. I plan to add "membership extensions" for these cases, but it's a little complicated and I didn't want to hold up this release for them. So Real Soon Now.

= 1.2.1 =
* Fixed bug where non-member admins would be redirected away from the "All Pages" page in the admin.

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
