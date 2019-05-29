<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2019, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_MANAGE_TRACKERS'	=> 'Manage trackers',

	'ALLOW_CLOSED_REPLY'			=> 'Allow closed replies',
	'ALLOW_CLOSED_REPLY_EXPLAIN'	=> 'If set to yes users will be able to reply to tickets that have already been closed.',

	'EDIT_TRACKER'			=> 'Edit tracker',
	'EDIT_TRACKER_EXPLAIN'	=> 'The form below will allow you to customise this tracker. Please note that moderation and other controls are set via permissions for each user or usergroup.',

	'LOG_TRACKER_EDIT'		=> '<strong>Edited tracker details</strong><br />» %s',
	'LOG_TRACKER_MOVE_DOWN'	=> '<strong>Moved </strong> %1$s <strong>below</strong> %2$s',
	'LOG_TRACKER_MOVE_UP'	=> '<strong>Moved </strong> %1$s <strong>above</strong> %2$s',

	'TRACKER_ACTIVE'			=> 'Tracker active',
	'TRACKER_ACTIVE_EXPLAIN'	=> 'Setting this to no will lock the tracker.',
	'TRACKER_ADMIN'				=> 'Trackers administration',
	'TRACKER_ADMIN_EXPLAIN'		=> 'Here you can edit, lock and unlock individual trackers as well as set certain additional controls. <strong>Note: Adding and deleting trackers is not available as these actions may cause unintended consequences. To prevent a tracker from being utilized, please lock that specific tracker.</strong>',
	'TRACKER_EMAIL'				=> 'Tracker email',
	'TRACKER_EMAIL_EXPLAIN'		=> 'This will be used as the address on all tracker emails.',
	'TRACKER_NAME'				=> 'Tracker name',
	'TRACKER_SETTINGS'			=> 'Tracker settings',
	'TRACKER_UPDATED'			=> 'Tracker information updated successfully.',
));
