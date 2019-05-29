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
 * Migration stage 1: Initial schema
 */
class m1_initial_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . tables::TRACKERS_TRACKER);
	}

	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	/**
	 * Add the table schema to the database
	 */
	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . tables::TRACKERS_ATTACHMENT	=> array(
					'COLUMNS'		=> array(
						'attachment_id'				=> array('UINT', null, 'auto_increment'),
						'post_id'					=> array('UINT', 0),
						'attachment_private'		=> array('BOOL', 0),
						'attachment_file'			=> array('VCHAR:40', ''),
						'attachment_title'			=> array('VCHAR:50', ''),
						'attachment_size'			=> array('INT:10', 0),
						'mimetype'					=> array('VCHAR:255', ''),
						'attachment_data'			=> array('MTEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'attachment_id',
					'KEYS'			=> array(
						'post_id'		=> array('INDEX', array('post_id')),
					),
				),

				$this->table_prefix . tables::TRACKERS_COMPONENT	=> array(
					'COLUMNS'		=> array(
						'component_id'				=> array('UINT', null, 'auto_increment'),
						'project_id'				=> array('UINT', 0),
						'component_name'			=> array('VCHAR:50', ''),
					),
					'PRIMARY_KEY'	=> 'component_id',
					'KEYS'			=> array(
						'project_id'	=> array('INDEX', array('project_id')),
					),
				),

				$this->table_prefix . tables::TRACKERS_HISTORY		=> array(
					'COLUMNS'		=> array(
						'history_id'				=> array('UINT', null, 'auto_increment'),
						'ticket_id'					=> array('UINT', 0),
						'user_id'					=> array('UINT', 0),
						'history_text'				=> array('MTEXT_UNI', ''),
						'history_timestamp'			=> array('TIMESTAMP', 0),
						'history_type'				=> array('TINT:3', 0),
					),
					'PRIMARY_KEY'	=> 'history_id',
				),

				$this->table_prefix . tables::TRACKERS_POST			=> array(
					'COLUMNS'		=> array(
						'post_id'					=> array('UINT', null, 'auto_increment'),
						'ticket_id'					=> array('UINT', 0),
						'user_id'					=> array('UINT', 0),
						'post_private'				=> array('BOOL', 0),
						'ticket_title'				=> array('VCHAR:100', ''),
						'post_text'					=> array('TEXT_UNI', ''),
						'post_ip'					=> array('VCHAR:40', ''),
						'post_timestamp'			=> array('TIMESTAMP', 0),
						'bbcode_bitfield'			=> array('VCHAR:255', ''),
						'bbcode_uid'				=> array('VCHAR:8', ''),
						'bbcode_flags'				=> array('INT:11', 0),
					),
					'PRIMARY_KEY'	=> 'post_id',
					'KEYS'			=> array(
						'ticket_id'		=> array('INDEX', array('ticket_id')),
					),
				),

				$this->table_prefix . tables::TRACKERS_PROJECT		=> array(
					'COLUMNS'		=> array(
						'project_id'				=> array('UINT', null, 'auto_increment'),
						'tracker_id'				=> array('UINT', 0),
						'project_name'				=> array('VCHAR:50', ''),
						'project_description'		=> array('VCHAR:255', ''),
						'project_note'				=> array('MTEXT_UNI', ''),
						'project_private'			=> array('BOOL', 0),
						'project_active'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'project_id',
				),

				$this->table_prefix . tables::TRACKERS_PROJECT_AUTH	=> array(
					'COLUMNS'		=> array(
						'project_id'				=> array('UINT', 0),
						'group_id'					=> array('UINT', 0),
						'user_id'					=> array('UINT', 0),
					),
				),

				$this->table_prefix . tables::TRACKERS_SEVERITY		=> array(
					'COLUMNS'		=> array(
						'severity_id'				=> array('UINT', null, 'auto_increment'),
						'tracker_id'				=> array('UINT', 0),
						'severity_name'				=> array('VCHAR:50', ''),
						'severity_colour'			=> array('VCHAR:6', ''),
						'severity_order'			=> array('TINT:2', 0),
					),
					'PRIMARY_KEY'	=> 'severity_id',
				),

				$this->table_prefix . tables::TRACKERS_STATUS		=> array(
					'COLUMNS'		=> array(
						'status_id'					=> array('UINT', null, 'auto_increment'),
						'tracker_id'				=> array('UINT', 0),
						'status_name'				=> array('VCHAR:50', ''),
						'status_description'		=> array('MTEXT_UNI', ''),
						'status_order'				=> array('UINT', 0),
						'ticket_new'				=> array('BOOL', 0),
						'ticket_reviewed'			=> array('BOOL', 0),
						'ticket_closed'				=> array('BOOL', 0),
						'ticket_fixed'				=> array('BOOL', 0),
						'ticket_duplicate'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'status_id',
				),

				$this->table_prefix . tables::TRACKERS_STATUS		=> array(
					'COLUMNS'		=> array(
						'status_id'					=> array('UINT', null, 'auto_increment'),
						'tracker_id'				=> array('UINT', 0),
						'status_name'				=> array('VCHAR:50', ''),
						'status_description'		=> array('MTEXT_UNI', ''),
						'status_order'				=> array('UINT', 0),
						'ticket_new'				=> array('BOOL', 0),
						'ticket_reviewed'			=> array('BOOL', 0),
						'ticket_closed'				=> array('BOOL', 0),
						'ticket_fixed'				=> array('BOOL', 0),
						'ticket_duplicate'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'status_id',
				),

				$this->table_prefix . tables::TRACKERS_TICKET		=> array(
					'COLUMNS'		=> array(
						'ticket_id'					=> array('UINT', null, 'auto_increment'),
						'project_id'				=> array('UINT', 0),
						'post_id'					=> array('UINT', 0),
						'user_id'					=> array('UINT', 0),
						'reporter_ip'				=> array('VCHAR:40', ''),
						'user_last_id'				=> array('UINT', 0),
						'status_id'					=> array('UINT', 0),
						'component_id'				=> array('UINT', 0),
						'severity_id'				=> array('UINT', 0),
						'duplicate_id'				=> array('UINT', 0),
						'ticket_private'			=> array('BOOL', 0),
						'ticket_title'				=> array('VCHAR:100', ''),
						'ticket_locked'				=> array('BOOL', 0),
						'assigned_group'			=> array('UINT', 0),
						'assigned_user'				=> array('UINT', 0),
						'timestamp_created'			=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY'	=> 'ticket_id',
					'KEYS'			=> array(
						'project_id'	=> array('INDEX', array('project_id')),
						'user_id'		=> array('INDEX', array('user_id')),
						'status_id'		=> array('INDEX', array('status_id')),
						'severity_id'	=> array('INDEX', array('severity_id')),
						'component_id'	=> array('INDEX', array('component_id')),
					),
				),

				$this->table_prefix . tables::TRACKERS_TRACKER		=> array(
					'COLUMNS'		=> array(
						'tracker_id'				=> array('UINT', null, 'auto_increment'),
						'tracker_name'				=> array('VCHAR:50', ''),
						'tracker_email'				=> array('VCHAR:150', ''),
						'allow_view_all'			=> array('BOOL', 0),
						'allow_closed_reply'		=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'tracker_id',
				),
			),
		);
	}

	/**
	 * Drop the table schema to the database
	 */
	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . tables::TRACKERS_ATTACHMENT,
				$this->table_prefix . tables::TRACKERS_COMPONENT,
				$this->table_prefix . tables::TRACKERS_HISTORY,
				$this->table_prefix . tables::TRACKERS_POST,
				$this->table_prefix . tables::TRACKERS_PROJECT,
				$this->table_prefix . tables::TRACKERS_PROJECT_AUTH,
				$this->table_prefix . tables::TRACKERS_SEVERITY,
				$this->table_prefix . tables::TRACKERS_STATUS,
				$this->table_prefix . tables::TRACKERS_TICKET,
				$this->table_prefix . tables::TRACKERS_TRACKER,
			),
		);
	}
}
