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
	'ACL_CAT_TRACKERS'	=> 'Trackers',

	'ACL_U_TRACKERS_ATTACH'	=> 'Can attach files',
	'ACL_U_TRACKERS_POST'	=> 'Can post new tickets',
	'ACL_U_TRACKERS_REPLY'	=> 'Can reply to tickets',
	'ACL_U_TRACKERS_EDIT'	=> 'Can edit own tickets/posts',
	'ACL_U_TRACKERS_DELETE'	=> 'Can delete own tickets/posts',

	'ACL_M_TRACKERS_EDIT'			=> 'Can edit tickets/posts<br /><em>This setting requires team access to the individual project.</em>',
	'ACL_M_TRACKERS_DELETE'			=> 'Can delete tickets/posts<br /><em>This setting requires team access to the individual project.',
	'ACL_M_TRACKERS_LOCK'			=> 'Can lock tickets<br /><em>This setting requires team access to the individual project.</em>',
	'ACL_M_TRACKERS_MOVE'			=> 'Can move tickets<br /><em>This setting requires team access to the individual project.</em>',
	'ACL_M_TRACKERS_ASSIGN'			=> 'Can assign tickets<br /><em>This setting requires team access to the individual project.</em>',
	'ACL_M_TRACKERS_CHGSTATUS'		=> 'Can change ticket status<br /><em>This setting requires team access to the individual project.</em>',
	'ACL_M_TRACKERS_CHGPRIORITY'	=> 'Can change ticket priority<br /><em>This setting requires team access to the individual project.</em>',
	'ACL_M_TRACKERS_CHGSEVERITY'	=> 'Can change ticket severity<br /><em>This setting requires team access to the individual project.</em>',

	'ACL_A_TRACKERS_MANAGE'	=> 'Can manage trackers',
));
