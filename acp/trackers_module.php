<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2019, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\trackers\acp;

use kinerity\trackers\tables;

/**
 * Trackers ACP module
 */
class trackers_module
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	public $page_title;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $u_action;

	public function main()
	{
		global $phpbb_container, $table_prefix;

		$this->cache = $phpbb_container->get('cache.driver');
		$this->db = $phpbb_container->get('dbal.conn');
		$this->language = $phpbb_container->get('language');
		$this->log = $phpbb_container->get('log');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');
		$this->table_prefix = $table_prefix;

		$this->language->add_lang('acp/forums');

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'acp_trackers';

		// Set the page title for our ACP page
		$this->page_title = 'ACP_MANAGE_TRACKERS';

		// Define the name of the form for use as a form key
		$form_name = 'acp_trackers';
		add_form_key($form_name);

		// Set an empty error array
		$errors = array();

		// Requests
		$action = $this->request->variable('action', '');
		$tracker_id = $this->request->variable('t', 0);

		switch ($action)
		{
			case 'edit':
				if (!$tracker_id)
				{
					trigger_error($this->language->lang('NO_TRACKER') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . $this->table_prefix . tables::TRACKERS_TRACKER . "
					WHERE tracker_id = $tracker_id";
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($this->language->lang('NO_TRACKER') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$this->page_title = 'EDIT_TRACKER';

				// If form is submitted
				if ($this->request->is_set_post('submit'))
				{
					// Test if form key is valid
					if (!check_form_key($form_name))
					{
						$errors[] = $this->language->lang('FORM_INVALID');
					}

					$sql_ary = array(
						'tracker_name'			=> $this->request->variable('tracker_name', '', true),
						'tracker_email'			=> $this->request->variable('tracker_name', '', true),
						'tracker_active'		=> $this->request->variable('tracker_active', 0),
						'allow_closed_reply'	=> $this->request->variable('allow_closed_reply', 0),
					);

					// Store the settings if submitted with no errors
					if (empty($errors))
					{
						$sql = 'UPDATE ' . $this->table_prefix . tables::TRACKERS_TRACKER . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE tracker_id = ' . (int) $tracker_id;
						$this->db->sql_query($sql);

						$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_TRACKER_' . strtoupper($action), false, array($sql_ary['tracker_name']));

						trigger_error($this->language->lang('TRACKER_UPDATED') . adm_back_link($this->u_action));
					}
				}

				$this->template->assign_vars(array(
					'S_EDIT_TRACKER'	=> true,
					'S_ERROR'			=> (count($errors)) ? true : false,

					'U_EDIT_ACTION'	=> $this->u_action . "&amp;t={$row['tracker_id']}&amp;action=$action",

					'L_TITLE'	=> $this->language->lang($this->page_title),
					'ERROR_MSG'	=> (count($errors)) ? implode('<br />', $errors) : '',

					'TRACKER_NAME'	=> $row['tracker_name'],
					'TRACKER_EMAIL'	=> $row['tracker_email'],

					'S_TRACKER_ACTIVE'		=> $row['tracker_active'],
					'S_ALLOW_CLOSED_REPLY'	=> $row['allow_closed_reply'],
				));
			break;

			case 'move_down':
			case 'move_up':
				if (!$tracker_id)
				{
					trigger_error($this->language->lang('NO_TRACKER') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . $this->table_prefix . tables::TRACKERS_TRACKER . "
					WHERE tracker_id = $tracker_id";
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($this->language->lang('NO_TRACKER') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$move_tracker_name = $this->move_tracker_by($row, $action, 1);

				if ($move_tracker_name !== false)
				{
					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_TRACKER_' . strtoupper($action), false, array($row['tracker_name'], $move_tracker_name));
					$this->cache->destroy('sql', $this->table_prefix . tables::TRACKERS_TRACKER);
				}

				if ($this->request->is_ajax())
				{
					$json_response = new \phpbb\json_response;
					$json_response->send(array('success' => ($move_tracker_name !== false)));
				}
			break;
		}

		$sql = 'SELECT *
			FROM ' . $this->table_prefix . tables::TRACKERS_TRACKER . '
			ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$folder_image = (!empty($row['tracker_active'])) ? '<img src="images/icon_folder.gif" alt="' . $this->language->lang('FOLDER') . '" />' : '<img src="images/icon_folder_lock.gif" alt="' . $this->language->lang('LOCKED') . '" />';

			$url = $this->u_action . "&amp;t={$row['tracker_id']}";

			$this->template->assign_block_vars('trackers', array(
				'FOLDER_IMAGE'	=> $folder_image,

				'TRACKER_NAME'	=> $row['tracker_name'],

				'U_MOVE_UP'		=> $url . '&amp;action=move_up',
				'U_MOVE_DOWN'	=> $url . '&amp;action=move_down',
				'U_EDIT'		=> $url . '&amp;action=edit',
			));
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Move tracker position by $steps up/down
	 */
	private function move_tracker_by($tracker_row, $action = 'move_up', $steps = 1)
	{
		/**
		 * Fetch all the siblings between the module's current spot
		 * and where we want to move it to. If there are less than $steps
		 * siblings between the current spot and the target then the
		 * module will move as far as possible
		 */
		$sql = 'SELECT tracker_id, tracker_name, left_id, right_id
			FROM ' . $this->table_prefix . tables::TRACKERS_TRACKER . "
			WHERE " . (($action == 'move_up') ? "right_id < {$tracker_row['right_id']} ORDER BY right_id DESC" : "left_id > {$tracker_row['left_id']} ORDER BY left_id ASC");
		$result = $this->db->sql_query_limit($sql, $steps);

		$target = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$target = $row;
		}
		$this->db->sql_freeresult($result);

		if (!count($target))
		{
			// The tracker is already on top or bottom
			return false;
		}

		/**
		 * $left_id and $right_id define the scope of the nodes that are affected by the move.
		 * $diff_up and $diff_down are the values to substract or add to each node's left_id
		 * and right_id in order to move them up or down.
		 * $move_up_left and $move_up_right define the scope of the nodes that are moving
		 * up. Other nodes in the scope of ($left_id, $right_id) are considered to move down.
		 */
		if ($action == 'move_up')
		{
			$left_id = $target['left_id'];
			$right_id = $tracker_row['right_id'];

			$diff_up = $tracker_row['left_id'] - $target['left_id'];
			$diff_down = $tracker_row['right_id'] + 1 - $tracker_row['left_id'];

			$move_up_left = $tracker_row['left_id'];
			$move_up_right = $tracker_row['right_id'];
		}
		else
		{
			$left_id = $tracker_row['left_id'];
			$right_id = $target['right_id'];

			$diff_up = $tracker_row['right_id'] + 1 - $tracker_row['left_id'];
			$diff_down = $target['right_id'] - $tracker_row['right_id'];

			$move_up_left = $tracker_row['right_id'] + 1;
			$move_up_right = $target['right_id'];
		}

		// Now do the dirty job
		$sql = 'UPDATE ' . $this->table_prefix . tables::TRACKERS_TRACKER . "
			SET left_id = left_id + CASE
				WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END,
			right_id = right_id + CASE
				WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END
			WHERE
				left_id BETWEEN {$left_id} AND {$right_id}
				AND right_id BETWEEN {$left_id} AND {$right_id}";
		$this->db->sql_query($sql);

		return $target['tracker_name'];
	}
}
