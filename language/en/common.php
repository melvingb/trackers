<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2020, kinerity, https://www.layer-3.org/
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
	$lang = [];
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

$lang = array_merge($lang, [
	'NO_PAGE_MODE'	=> 'Invalid or no page mode specified.',

	'TRACKERS'	=> 'Trackers',

	// Viewtracker
	'NO_PROJECTS'	=> 'There are no projects available for this tracker.',
	'NO_TRACKER'	=> 'The requested tracker does not exist.',

	'TRACKER_EXPLAIN'	=> 'Please select the project you would like to open below.',
	'TRACKER_PRIVATE'	=> 'Note: Submissions to this tracker are private; only you and team members that have access to the specified tracker will see the information.',

	'STATISTICS'	=> 'Statistics',

	// Viewproject
	'ALL_CLOSED'	=> 'All closed tickets',
	'ALL_OPEN'		=> 'All open tickets',
	'ALL_TICKETS'	=> 'All tickets',
	'ASSIGNED'		=> 'Assigned to',

	'BUTTON_NEW_TICKET'	=> 'New Ticket',

	'CHANGE_SEVERITY'	=> 'Change ticket severity',
	'CHANGE_STATUS'		=> 'Change ticket status',
	'COMPONENT'			=> 'Component',
	'CURRENT_STATUS'	=> 'Currently showing',

	'FILTER_TICKETS'	=> 'Filter tickets',

	'NO_TICKETS'	=> 'There are no tickets to display.',

	'POST_TICKET'	=> 'Post a new ticket',

	'STATUS'	=> 'Status',

	'TITLE'			=> 'Title',
	'TOTAL_TICKETS'	=> [
		0	=> '0 tickets',
		1	=> '1 ticket',
		2	=> '%d tickets',
	],

	'UNASSIGNED'	=> 'unassigned',
	'UNKNOWN'		=> 'unknown',

	// Viewticket
	'COMMENTS'	=> 'Comments',

	'DUP_TICKET'	=> 'Duplicates of this ticket',
	'DUP_OTHER'		=> 'Duplicates of ',

	'HISTORY'	=> 'History',

	'NO_ENTRIES'	=> 'No comments have been made and there are no history entries.',

	'PAGE_TOTAL_POSTS'	=> [
		0	=> '0 posts',
		1	=> '1 post',
		2	=> '%d posts',
	],
	'POSTED_BY'	=> 'Posted by',
	'PRIVATE'	=> 'Private',
	'PROJECT'	=> 'Project',

	'REPORTED_BY'	=> 'Reported by',
	'REPORTED_ON'	=> 'Reported on',

	'SEND_PM'	=> 'Send PM',
	'SEVERITY'	=> 'Severity',

	'TICKET_ACTION'		=> 'Action performed by',
	'TICKET_DETAILS'	=> 'Ticket details',
	'TICKET_ID'			=> 'Ticket ID',

	'UNCATEGORISED'	=> 'Uncategorised/normal',

	// Statistics
	'CLOSED_STATUS'		=> 'Is "closed" status',
	'CLOSED_TICKETS'	=> 'Closed tickets',

	'FILTER_BETWEEN'	=> 'tickets that were submitted between %1$s and %2$s',
	'FILTER_AFTER'		=> 'tickets that were submitted after %1$s',
	'FILTER_BEFORE'		=> 'tickets that were submitted before %1$s',

	'NO_STATS'			=> 'There are no statistics for this tracker.',
	'NUMBER_TICKETS'	=> 'Number of tickets',

	'OPEN_TICKETS'	=> 'Open tickets',

	'PROJECTS_ALL'	=> 'All projects',

	'STATISTICS_PROJECT_EXPLAIN'	=> 'Below you can find the statistics for the %1$s project in the %2$s %3$s. These statistics include ',
	'STATISTICS_TRACKER_EXPLAIN'	=> 'Below you can find the statistics for the %1$s at %2$s. These statistics will give you a general idea of the number of tickets that are currently open and closed, and allows you to get this data for a given month.<br /><br />You may obtain more detailed statistics by selecting a specific project from the list below.',
	'STATUS_OVERVIEW'				=> 'Ticket status overview',

	'TIMESPAN_TICKETS'		=> 'Tickets submitted between %1$s and %2$s',
	'TOTALS'				=> 'Totals',
	'TRACKER_STATISTICS'	=> 'Tracker statistics',

	// Changed vars
	'CHANGED_STATUS'	=> 'Changed ticket status from "%1$s" to "%2$s"',
	'CHANGED_SEVERITY'	=> 'Changed ticket severity from "%1$s" to "%2$s"',
	'CHANGED_COMPONENT'	=> 'Changed ticket component from "%1$s" to "%2$s"',
	'CHANGED_ASSIGN'	=> 'Assigned ticket to %1$s %2$s',
]);
