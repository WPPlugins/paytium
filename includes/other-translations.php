<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Other translation strings for Paytium.
 *
 * @author        David de Boer
 * @since         1.4.0
 */

/**
 * Paytium PP main file
 * 2017/03/18
 */

__( 'Paytium Plus, Pro or Premium need the main plugin to work! Go to Plugins > Add new and search for "Paytium" to install it.', 'paytium' ) ;
__( 'You already have Paytium Plus, Pro or Premium enabled. You only need one of them, besides the Paytium main plugin.', 'paytium' );


/**
 * Paytium License Library
 * 2017/02/05
 */

__( '%s license:', 'paytium' );
__( 'Your license key', 'paytium' );
__( 'Your license key has expired, please renew it through our system', 'paytium' );
__( 'Your license has expired.', 'paytium' );
__( 'Please renew it to receive plugin updates.', 'paytium' );
__( 'Enter your license info and press Enter to activate it', 'paytium' );
__( 'Your license has been revoked', 'paytium' );
__( 'Invalid license key, please verify your license and try again', 'paytium' );
__( 'Your license is not active for this URL', 'paytium' );
__( 'This license is not valid for %s', 'paytium' );
__( 'Your license has reached its activation limit', 'paytium' );
__( 'Something went wrong, please verify everything and try again', 'paytium' );
__( 'There is a new version of %s available.' );
__( 'Please enter a valid license to receive this update.', 'paytium' );
__( 'Your license for %s is active.', 'paytium' );
__( 'Deactivate license', 'paytium');

/**
 * Paytium MailChimp
 * 2016/12/13
 */

__( 'Settings', 'paytium' );
__( 'Manual', 'paytium' );

__( 'Check out the Paytium MailChimp manual', 'paytium' );
__( 'Only subscribe after successful payment', 'paytium' );
__( 'If you only want people to be subscribed after a successful payment, check this. If you do not check this, people will be subscribed before payment.', 'paytium' );
__( 'API key', 'paytium' );
__( 'Go to MailChimp.com to get your API key', 'paytium' );
__( 'Your MailChimp Account', 'paytium' );
__( 'Below you will see the names and ID\'s of lists and groups you have in your MailChimp account. Use the ID\'s to let Paytium know how a subscriber should be added to your MailChimp account.', 'paytium' );
__( 'Default List ID', 'paytium' );
__( 'To what MailChimp List should subscribers be added by default? It\'s not required to set this here, it\'s okay if you only set the List ID in the shortcode. And you can always override the list ID in the shortcode for specific payment forms.', 'paytium' );
__( 'Default Group ID ', 'paytium' );
__( 'If you use Groups in MailChimp, set to what Group subscribers should be added by default. It\'s not required to set this here, it\'s okay if you only set the Group ID in the shortcode. And you can always override the group ID in the shortcode for specific payment forms.', 'paytium' );

__( 'Add your MailChimp API key above and Paytium will get your MailChimp account details. Saves you a lot of clicking :)', 'paytium' );

__( 'List:', 'paytium' );
__( 'Group category:', 'paytium' );
__( 'Group:', 'paytium' );

__( 'Don\'t show a "Subscribe to newsletter?" checkbox', 'paytium' );
__( 'If you check this, users will not see a "Subscribe to newsletter?" checkbox in the payment form, and will always be added to a newsletter (if all other conditions are met). If you leave this unchecked, users will see a "Subscribe to newsletter?" checkbox in the payment form and can check that checkbox if they want to be added to the newsletter. Showing an opt-in checkbox is mandatory by law in some cases.', 'paytium' );

/**
 * Paytium Emails
 * 2017/02/02
 */

__( 'Manage Emails', 'paytium' );
__( 'Manual', 'paytium' );

__( 'Emails', 'paytium' );
__( 'Email', 'paytium' );

__( 'Add New', 'paytium' );
__( 'Add New Email', 'paytium' );
__( 'New Email', 'paytium' );
__( 'Edit Email', 'paytium' );
__( 'View Email', 'paytium' );

__( 'Search Emails', 'paytium' );
__( 'Parent Emails:', 'paytium' );
__( 'No emails found.', 'paytium' );
__( 'No emails found in Trash.', 'paytium' );

__( 'Paytium Emails', 'paytium' );

__( 'Email updated.', 'paytium' );
__( 'Email restored to revision from %s', 'paytium' );
__( 'Email saved.', 'paytium' );

__( 'Subject', 'paytium' );
__( 'Email trigger(s)', 'paytium' );
__( 'Recipients', 'paytium' );
__( 'Status', 'paytium' );

__( 'Open payments', 'paytium' );
__( 'Cancelled payments', 'paytium' );
__( 'Pending payments', 'paytium' );
__( 'Expired payments', 'paytium' );
__( 'Paid payments', 'paytium' );
__( 'Refunded payments', 'paytium' );

__( 'Email triggers', 'paytium' );
__( 'Tags for default information', 'paytium' );
__( 'Tags for custom fields', 'paytium' );

__( 'Looks like you don\'t have Paytium installed and activated, please fix that!', 'paytium' );
__( 'Place a first (test) payment and Paytium will show you what tags can be used as placeholders in emails.', 'paytium' );

__( 'These are all the default tags that you can use, based on the most recent payment #', 'paytium' );
__( 'These are the custom tags that you can use, based on most recent payment #', 'paytium' );
__( 'Click on a tag to insert it into your email.', 'paytium' );

__( 'Default tags', 'paytium' );
__( 'Content', 'paytium' );

__( 'Subscription tags', 'paytium' );

__( 'Your last payment was not a subscription, so those tags aren\'t shown.', 'paytium' );

__( 'Custom field: ', 'paytium' );
__( 'Enable this email notification', 'paytium' );

__( 'Send to customer(s)', 'paytium' );
__( 'If a field with type "email" is found within the payment form and the \'Send to customer(s)\' checkbox is checked, this email will be automatically sent to the customer\'s email address.', 'paytium' );
__( 'List the (other) desired recipients for this email, one email address per line.', 'paytium' );

__( 'Enter email notification subject', 'paytium' );







