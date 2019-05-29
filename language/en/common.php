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
	'ALL_CLOSED'	=> 'All closed tickets',
	'ALL_OPEN'		=> 'All open tickets',
	'ALL_TICKETS'	=> 'All tickets',
	'ASSIGNED_TO'	=> 'Assigned to',

	'BUTTON_NEW_TICKET'	=> 'New Ticket',

	'COMPONENT'	=> 'Component',

	'DESCRIPTION'	=> 'Description',

	'FILTER_STATUS'		=> 'Currently showing',
	'FILTER_TICKETS'	=> 'Filter tickets',

	'LOCKED'	=> 'Locked',

	'NO_PAGE_MODE'		=> 'Invalid or no page mode specified.',
	'NO_POSTS_HISTORY'	=> 'No comments have been made and there are no history entries.',
	'NO_PROJECTS'		=> 'There are no projects available for this tracker.',
	'NO_TICKETS'		=> 'There are no tickets available for this project.',
	'NO_TRACKER'		=> 'The requested tracker does not exist.',

	'POST_TICKET'	=> 'Post a new ticket',
	'POSTS_HISTORY'	=> 'Comments / History',
	'PRIVATE'		=> 'Private',
	'PROJECT'		=> 'Project',

	'REPORTED_BY'	=> 'Reported by',
	'REPORTED_FROM'	=> 'Reported from',
	'REPORTED_ON'	=> 'Reported on',

	'SEVERITY'		=> 'Severity',
	'STATISTICS'	=> 'Statistics',
	'STATUS'		=> 'Status',

	'TICKET_ID'			=> 'Ticket ID',
	'PAGE_TOTAL_POSTS'	=> array(
		0	=> '0 posts',
		1	=> '1 post',
		2	=> '%d posts',
	),
	'TOTAL_TICKETS'		=> array(
		0	=> '0 tickets',
		1	=> '1 ticket',
		2	=> '%d tickets',
	),
	'TRACKERS'			=> 'Trackers',
	'TRACKER_EXPLAIN'	=> 'Please select the project you would like to open below.',
	'TRACKER_PRIVATE'	=> 'Note: Submissions to this tracker are private; only you and team members that have access to the specified tracker will see the information.',

	'UNASSIGNED'	=> 'Unassigned',
	'UNCATEGORISED'	=> 'Uncategorised',
	'UNKNOWN'		=> 'Unknown',
));
