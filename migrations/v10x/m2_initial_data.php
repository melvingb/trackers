<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2019, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\trackers\migrations\v10x;

use kinerity\trackers\tables;

/**
 * Migration stage 2: Initial data
 */
class m2_initial_data extends \phpbb\db\migration\container_aware_migration
{
	public static function depends_on()
	{
		return array('\kinerity\trackers\migrations\v10x\m1_initial_schema');
	}

	/**
	 * Add the data to the database
	 */
	public function update_data()
	{
		$data = array(
			// Add config data
			array('config.add', array('tickets_per_page', 25)),

			// Call a custom callable function to perform data insertion
			array('custom', array(array($this, 'insert_data'))),
		);

		return $data;
	}

	/**
	 * A custom function for inserting default data values
	 */
	public function insert_data()
	{
		$severity_data = array(
			array(
				'severity_id'		=> 5,
				'tracker_id'		=> 2,
				'severity_name'		=> 'Severe',
				'severity_colour'	=> 'ECD5D8',
				'severity_order'	=> 1,
			),

			array(
				'severity_id'		=> 15,
				'tracker_id'		=> 3,
				'severity_name'		=> 'Severe',
				'severity_colour'	=> 'ECD5D8',
				'severity_order'	=> 1,
			),

			array(
				'severity_id'		=> 25,
				'tracker_id'		=> 2,
				'severity_name'		=> 'Possibly invalid',
				'severity_colour'	=> 'fde8a2',
				'severity_order'	=> 2,
			),

			array(
				'severity_id'		=> 35,
				'tracker_id'		=> 2,
				'severity_name'		=> 'Possibly invalid',
				'severity_colour'	=> 'fde8a2',
				'severity_order'	=> 2,
			),
		);

		$status_data = array(
			array(
				'status_id'				=> 1,
				'tracker_id'			=> 1,
				'status_name'			=> 'New',
				'status_description'	=> '',
				'status_order'			=> 1,
				'ticket_new'			=> 1,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 2,
				'tracker_id'			=> 1,
				'status_name'			=> 'Possible bug',
				'status_description'	=> '',
				'status_order'			=> 2,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 3,
				'tracker_id'			=> 1,
				'status_name'			=> 'Possible security issue',
				'status_description'	=> '',
				'status_order'			=> 3,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 4,
				'tracker_id'			=> 1,
				'status_name'			=> 'Reviewed',
				'status_description'	=> '',
				'status_order'			=> 4,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 1,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 5,
				'tracker_id'			=> 1,
				'status_name'			=> 'Awaiting information',
				'status_description'	=> '',
				'status_order'			=> 5,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 6,
				'tracker_id'			=> 1,
				'status_name'			=> 'Support request',
				'status_description'	=> '',
				'status_order'			=> 6,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 7,
				'tracker_id'			=> 1,
				'status_name'			=> 'Awaiting team input',
				'status_description'	=> '',
				'status_order'			=> 7,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 8,
				'tracker_id'			=> 1,
				'status_name'			=> 'Closed',
				'status_description'	=> '',
				'status_order'			=> 8,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 9,
				'tracker_id'			=> 2,
				'status_name'			=> 'New',
				'status_description'	=> '',
				'status_order'			=> 1,
				'ticket_new'			=> 1,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 10,
				'tracker_id'			=> 2,
				'status_name'			=> 'Duplicate',
				'status_description'	=> '',
				'status_order'			=> 2,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 1,
			),

			array(
				'status_id'				=> 11,
				'tracker_id'			=> 2,
				'status_name'			=> 'Fixed',
				'status_description'	=> '',
				'status_order'			=> 3,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 1,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 12,
				'tracker_id'			=> 2,
				'status_name'			=> 'Bug',
				'status_description'	=> '',
				'status_order'			=> 4,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 13,
				'tracker_id'			=> 2,
				'status_name'			=> 'Support request',
				'status_description'	=> '',
				'status_order'			=> 5,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 14,
				'tracker_id'			=> 2,
				'status_name'			=> 'Invalid',
				'status_description'	=> '',
				'status_order'			=> 6,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 15,
				'tracker_id'			=> 2,
				'status_name'			=> 'Reviewed',
				'status_description'	=> '',
				'status_order'			=> 7,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 1,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 16,
				'tracker_id'			=> 2,
				'status_name'			=> 'Awaiting information',
				'status_description'	=> '',
				'status_order'			=> 8,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 17,
				'tracker_id'			=> 2,
				'status_name'			=> 'Awaiting team input',
				'status_description'	=> '',
				'status_order'			=> 9,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 18,
				'tracker_id'			=> 2,
				'status_name'			=> 'Patching in progress',
				'status_description'	=> '',
				'status_order'			=> 10,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 19,
				'tracker_id'			=> 2,
				'status_name'			=> 'Patch written',
				'status_description'	=> '',
				'status_order'			=> 11,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 20,
				'tracker_id'			=> 2,
				'status_name'			=> 'Closed',
				'status_description'	=> '',
				'status_order'			=> 12,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 21,
				'tracker_id'			=> 3,
				'status_name'			=> 'New',
				'status_description'	=> '',
				'status_order'			=> 1,
				'ticket_new'			=> 1,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 22,
				'tracker_id'			=> 3,
				'status_name'			=> 'Not a bug',
				'status_description'	=> '',
				'status_order'			=> 2,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 23,
				'tracker_id'			=> 3,
				'status_name'			=> 'Support request',
				'status_description'	=> '',
				'status_order'			=> 3,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 24,
				'tracker_id'			=> 3,
				'status_name'			=> 'Duplicate',
				'status_description'	=> '',
				'status_order'			=> 4,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 1,
			),

			array(
				'status_id'				=> 25,
				'tracker_id'			=> 3,
				'status_name'			=> 'Already fixed',
				'status_description'	=> '',
				'status_order'			=> 5,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 26,
				'tracker_id'			=> 3,
				'status_name'			=> 'Reviewed',
				'status_description'	=> '',
				'status_order'			=> 6,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 1,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 27,
				'tracker_id'			=> 3,
				'status_name'			=> 'Review later',
				'status_description'	=> '',
				'status_order'			=> 7,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 28,
				'tracker_id'			=> 3,
				'status_name'			=> 'Awaiting information',
				'status_description'	=> '',
				'status_order'			=> 8,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 29,
				'tracker_id'			=> 3,
				'status_name'			=> 'Awaiting team input',
				'status_description'	=> '',
				'status_order'			=> 9,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 30,
				'tracker_id'			=> 3,
				'status_name'			=> 'Pending',
				'status_description'	=> '',
				'status_order'			=> 10,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 31,
				'tracker_id'			=> 3,
				'status_name'			=> 'Will not fix',
				'status_description'	=> '',
				'status_order'			=> 11,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 32,
				'tracker_id'			=> 3,
				'status_name'			=> 'Fix in progress',
				'status_description'	=> '',
				'status_order'			=> 12,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 33,
				'tracker_id'			=> 3,
				'status_name'			=> 'Fix completed in VCS',
				'status_description'	=> '',
				'status_order'			=> 13,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 1,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 34,
				'tracker_id'			=> 3,
				'status_name'			=> 'Unreproducible',
				'status_description'	=> '',
				'status_order'			=> 14,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 35,
				'tracker_id'			=> 4,
				'status_name'			=> 'New',
				'status_description'	=> '',
				'status_order'			=> 1,
				'ticket_new'			=> 1,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 36,
				'tracker_id'			=> 4,
				'status_name'			=> 'Support request',
				'status_description'	=> '',
				'status_order'			=> 2,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 37,
				'tracker_id'			=> 4,
				'status_name'			=> 'Invalid',
				'status_description'	=> '',
				'status_order'			=> 3,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 38,
				'tracker_id'			=> 4,
				'status_name'			=> 'Duplicate',
				'status_description'	=> '',
				'status_order'			=> 4,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 1,
			),

			array(
				'status_id'				=> 39,
				'tracker_id'			=> 4,
				'status_name'			=> 'Implementing',
				'status_description'	=> '',
				'status_order'			=> 5,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 40,
				'tracker_id'			=> 4,
				'status_name'			=> 'Will not implement',
				'status_description'	=> '',
				'status_order'			=> 6,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 41,
				'tracker_id'			=> 4,
				'status_name'			=> 'Implemented in VCS',
				'status_description'	=> '',
				'status_order'			=> 7,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 1,
				'ticket_fixed'			=> 1,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 42,
				'tracker_id'			=> 4,
				'status_name'			=> 'Researching',
				'status_description'	=> '',
				'status_order'			=> 8,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 43,
				'tracker_id'			=> 4,
				'status_name'			=> 'Reviewed',
				'status_description'	=> '',
				'status_order'			=> 9,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 1,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 44,
				'tracker_id'			=> 4,
				'status_name'			=> 'Review later',
				'status_description'	=> '',
				'status_order'			=> 10,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 45,
				'tracker_id'			=> 4,
				'status_name'			=> 'Awaiting information',
				'status_description'	=> '',
				'status_order'			=> 11,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 46,
				'tracker_id'			=> 4,
				'status_name'			=> 'Awaiting team input',
				'status_description'	=> '',
				'status_order'			=> 12,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),

			array(
				'status_id'				=> 47,
				'tracker_id'			=> 4,
				'status_name'			=> 'Pending',
				'status_description'	=> '',
				'status_order'			=> 13,
				'ticket_new'			=> 0,
				'ticket_reviewed'		=> 0,
				'ticket_closed'			=> 0,
				'ticket_fixed'			=> 0,
				'ticket_duplicate'		=> 0,
			),
		);

		$domain = strstr($this->config['board_email'], '@');

		$tracker_data = array(
			array(
				'tracker_id'			=> 1,
				'tracker_name'			=> 'Incident tracker',
				'tracker_email'			=> 'incidents' . $domain,
				'allow_view_all'		=> 0,
				'allow_closed_reply'	=> 1,
			),

			array(
				'tracker_id'			=> 2,
				'tracker_name'			=> 'Security tracker',
				'tracker_email'			=> 'security' . $domain,
				'allow_view_all'		=> 0,
				'allow_closed_reply'	=> 1,
			),

			array(
				'tracker_id'			=> 3,
				'tracker_name'			=> 'Bug tracker',
				'tracker_email'			=> 'bugs' . $domain,
				'allow_view_all'		=> 1,
				'allow_closed_reply'	=> 1,
			),

			array(
				'tracker_id'			=> 4,
				'tracker_name'			=> 'Feature tracker',
				'tracker_email'			=> 'features' . $domain,
				'allow_view_all'		=> 1,
				'allow_closed_reply'	=> 1,
			),
		);

		$this->db->sql_multi_insert($this->table_prefix . tables::TRACKERS_SEVERITY, $severity_data);
		$this->db->sql_multi_insert($this->table_prefix . tables::TRACKERS_STATUS, $status_data);
		$this->db->sql_multi_insert($this->table_prefix . tables::TRACKERS_TRACKER, $tracker_data);
	}
}
