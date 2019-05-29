<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2019, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\trackers\includes;

use Symfony\Component\DependencyInjection\ContainerInterface;
use kinerity\trackers\tables;
use kinerity\trackers\constants;

class functions_tracker
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var ContainerInterface */
	protected $container;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var string */
	protected $root_path;

	/* @var string */
	protected $php_ext;

	/* @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth                   $auth
	 * @param \phpbb\config\config               $config
	 * @param ContainerInterface                 $container
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\request\request             $request
	 * @param \phpbb\template\template           $template
	 * @param \phpbb\user                        $user
	 * @param string                             $root_path
	 * @param string                             $php_ext
	 * @param string                             $table_prefix
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, ContainerInterface $container, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $root_path, $php_ext, $table_prefix)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->helper = $helper;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}

	public function get_tracker_data($tracker_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_prefix . tables::TRACKERS_TRACKER . '
			WHERE tracker_id = ' . (int) $tracker_id;
		$result = $this->db->sql_query($sql);
		$tracker = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$tracker = $row;
		}
		$this->db->sql_freeresult($result);

		if (empty($tracker) || $tracker['tracker_status'] == ITEM_LOCKED)
		{
			throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_TRACKER'));
		}

		return $tracker;
	}

	public function get_projects($tracker_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_prefix . tables::TRACKERS_PROJECT . '
			WHERE tracker_id = ' . (int) $tracker_id;
		$result = $this->db->sql_query($sql);
		$projects = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$projects[$row['project_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $projects;
	}

	public function get_project_data($project_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_prefix . tables::TRACKERS_PROJECT . '
			WHERE project_id = ' . (int) $project_id;
		$result = $this->db->sql_query($sql);
		$project = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$project = $row;
		}
		$this->db->sql_freeresult($result);

		if (empty($project))
		{
			throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_PROJECT'));
		}

		return $project;
	}

	public function get_statuses($tracker_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_prefix . tables::TRACKERS_STATUS . '
			WHERE tracker_id = ' . (int) $tracker_id . '
			ORDER BY status_order ASC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$statuses[$row['tracker_id']][$row['status_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $statuses[$tracker_id];
	}

	public function get_status_data($status_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_prefix . tables::TRACKERS_STATUS . '
			WHERE status_id = ' . (int) $status_id;
		$result = $this->db->sql_query($sql);
		$status = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$status = $row;
		}
		$this->db->sql_freeresult($result);

		return $status;
	}

	public function get_total_tickets($sql)
	{
		$result = $this->db->sql_query('SELECT t.user_id AS ticket_user_id ' . $sql);
		$total_tickets = $this->db->sql_affectedrows();
		$this->db->sql_freeresult($result);

		return $total_tickets;
	}

	public function get_total_posts($ticket_id)
	{
		// Get the total number of posts for this ticket, minus one because the ticket is also a separate post
		$sql = 'SELECT COUNT(post_id) AS posts_total
			FROM ' . $this->table_prefix . tables::TRACKERS_POST . '
			WHERE ticket_id = ' . (int) $ticket_id;

		if (!$this->is_team_user())
		{
			// Make sure not to show pagination to normal users if there are private posts
			$sql .= ' AND post_private = 0';
		}

		$result = $this->db->sql_query($sql);
		$posts_total = $this->db->sql_fetchfield('posts_total', 0, $result) - 1;
		$this->db->sql_freeresult($result);

		return $posts_total;
	}

	public function get_tickets($sql, $start, $status_new)
	{
		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);

		// Get the tracker_id and project_id again (saves db queries)
		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);

		$sql = 'SELECT t.ticket_id, t.status_id, t.ticket_private, t.ticket_title, t.duplicate_id, st.status_name, st.status_id, st.ticket_duplicate, se.severity_colour, c.component_name, t.timestamp_created, t.assigned_user, t.assigned_group,
			t.user_id AS reporter_id, r.username AS reporter_username, r.user_colour AS reporter_colour,
			t.user_last_id AS lposter_id, lp.username AS lposter_username, lp.user_colour AS lposter_colour, MAX(tp.post_timestamp) AS lpost_time ' . $sql;
		$result = $this->db->sql_query_limit($sql, $this->config['tickets_per_page'], $start);
		while ($row = $this->db->sql_fetchrow($result))
		{
			// Figure out who this ticket was assigned to
			$assigned_to = '';
			if (!empty($row['assigned_user']))
			{
				$assigned_to = $this->get_user_data($row['assigned_user']);
			}
			else if (!empty($row['assigned_group']))
			{
				$assigned_to = $this->get_group_data($row['assigned_group']);
			}

			$this->template->assign_block_vars('ticketrow', array(
				'S_NEW'		=> ($row['status_id'] == $status_new) ? true : false,
				'S_PRIVATE'	=> $row['ticket_private'],

				'U_VIEWTICKET'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $row['ticket_id'])),
				'U_DUPLICATE'	=> ($row['ticket_duplicate'] && $row['duplicate_id']) ? $this->helper->route('kinerity_trackers_controller', array('page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $row['duplicate_id'])) : '',

				'ID'			=> $row['ticket_id'],
				'REPORTER'		=> $this->get_user_data($row['reporter_id']),
				'LPOSTER'		=> ($row['lposter_id'] != 0) ? $this->get_user_data($row['lposter_id']) : '',
				'LTIMESTAMP'	=> ($row['lposter_id'] != 0) ? $this->user->format_date($row['lpost_time']) : '',
				'COMPONENT'		=> $row['component_name'],
				'ASSIGNED'		=> $assigned_to,
				'TITLE'			=> $row['ticket_title'],
				'STATUS'		=> $row['status_name'],
				'DUPLICATE_ID'	=> ($row['ticket_duplicate']) ? $row['duplicate_id'] : false,
				'TIMESTAMP'		=> $this->user->format_date($row['timestamp_created']),
				'SEV_COLOUR'	=> $row['severity_colour'],
			));
		}
		$this->db->sql_freeresult($result);
	}

	public function get_ticket_data($ticket_id, $project_id = 0)
	{
		$sql = 'SELECT t.*, u.user_id AS reporter_id, se.severity_name, st.status_name, st.ticket_closed, st.ticket_duplicate, c.component_name, au.user_id, ag.group_id
			FROM (' . $this->table_prefix . tables::TRACKERS_TICKET . ' t, ' . USERS_TABLE . ' u)
			LEFT JOIN ' . $this->table_prefix . tables::TRACKERS_SEVERITY . ' se
				ON se.severity_id = t.severity_id
			LEFT JOIN ' . $this->table_prefix . tables::TRACKERS_STATUS . ' st
				ON st.status_id = t.status_id
			LEFT JOIN ' . $this->table_prefix . tables::TRACKERS_COMPONENT . ' c
				ON c.component_id = t.component_id
			LEFT JOIN ' . USERS_TABLE . ' au
				ON au.user_id = t.assigned_user
			LEFT JOIN ' . GROUPS_TABLE . ' ag
				ON ag.group_id = t.assigned_group
			WHERE u.user_id = t.user_id
				AND t.ticket_id = ' . (int) $ticket_id;

		if ($project_id > 0)
		{
			$sql .= ' AND t.project_id = ' . (int) $project_id;
		}

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// First make sure there's a ticket
		if (empty($row))
		{
			throw new \phpbb\exception\http_exception(404, $this->language->lang('NO_TICKET'));
		}

		// Figure out who this ticket was assigned to
		$assigned_to = '';
		if (!empty($row['assigned_user']))
		{
			$row['assigned'] = $this->get_user_data($row['assigned_user']);
		}
		else if (!empty($row['assigned_group']))
		{
			$row['assigned'] = $this->get_group_data($row['assigned_group']);
		}

		return $row;
	}

	public function tickets_sql($tracker, $project_id, $ticket_status)
	{
		$sql = 'FROM (' . $this->table_prefix . tables::TRACKERS_TICKET . ' t, ' . $this->table_prefix . tables::TRACKERS_STATUS . ' st)
			LEFT JOIN ' . USERS_TABLE . ' r
				ON r.user_id = t.user_id
			LEFT JOIN ' . USERS_TABLE . ' lp
				ON lp.user_id = t.user_last_id
			LEFT JOIN ' . USERS_TABLE . ' au
				ON au.user_id = t.assigned_user
			LEFT JOIN ' . GROUPS_TABLE . ' ag
				ON ag.group_id = t.assigned_group
			LEFT JOIN ' . $this->table_prefix . tables::TRACKERS_SEVERITY . ' se
				ON se.severity_id = t.severity_id
			LEFT JOIN ' . $this->table_prefix . tables::TRACKERS_COMPONENT . ' c
				ON c.component_id = t.component_id
			LEFT JOIN ' . $this->table_prefix . tables::TRACKERS_POST . ' tp
				ON tp.ticket_id = t.ticket_id
			WHERE st.status_id = t.status_id
				AND t.project_id = ' . (int) $project_id;

		if ($ticket_status == constants::STATUS_OPEN)
		{
			$sql .= ' AND st.ticket_closed = 0';
		}
		else if ($ticket_status == constants::STATUS_CLOSED)
		{
			$sql .= ' AND st.ticket_closed = 1';
		}
		else if ($ticket_status != constants::STATUS_ALL && ($tracker['allow_view_all'] || $this->is_team_user()))
		{
			$sql .=' AND t.status_id = ' . (int) $ticket_status;
		}

		// Restrict visible tickets
		if (!$tracker['allow_view_all'] && !$this->is_team_user())
		{
			$sql .= ' AND t.user_id = ' . $this->user->data['user_id'];
		}

		// Hide private tickets from non-team users
		if (!$this->is_team_user() && !$this->can_report_private())
		{
			$sql .= ' AND (t.ticket_private = 0)';
		}

		$sql .= ' GROUP BY t.ticket_id ORDER BY t.ticket_id DESC';

		return $sql;
	}

	public function get_ticket_text($ticket_id, $decode_message = false)
	{
		$sql = 'SELECT post_id, post_text, bbcode_bitfield, bbcode_uid, bbcode_flags
			FROM ' . $this->table_prefix . tables::TRACKERS_POST . '
			WHERE post_private = 0
				AND ticket_id = ' . (int) $ticket_id . '
			ORDER BY post_id ASC';
		$result = $this->db->sql_query_limit($sql, 1);
		$post_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$post_text = $post_data['post_text'];

		if (!$decode_message)
		{
			$post_text = generate_text_for_display($post_text, $post_data['bbcode_uid'], $post_data['bbcode_bitfield'], $post_data['bbcode_flags']);
		}
		else
		{
			decode_message($post_text, $post_data['bbcode_uid']);
		}

		return $post_text;
	}

	public function can_report_private()
	{
		if (!$this->user->data['user_id'] == ANONYMOUS)
		{
			if ($this->auth->acl_get('a_') || $auth->acl_getf_global('m_'))
			{
				return true;
			}
		}

		if ($this->is_team_user())
		{
			return true;
		}

		return false;
	}

	public function can_set_severity()
	{
		if (!$this->user->data['user_id'] == ANONYMOUS)
		{
			if ($this->auth->acl_get('a_') || $auth->acl_getf_global('m_'))
			{
				return true;
			}
		}

		if ($this->is_team_user())
		{
			return true;
		}

		return false;
	}

	public function get_ticket_details($project, $ticket)
	{
		// Ticket details
		$ticket_details = array();
		$ticket_details[$this->language->lang('TICKET_ID')] = $ticket['ticket_id'];
		$ticket_details[$this->language->lang('PROJECT')] = $project['project_name'];
		$ticket_details[$this->language->lang('STATUS')] = $ticket['status_name'];

		if ($ticket['ticket_duplicate'] && $ticket['duplicate_id'] > 0)
		{
			$ticket_details[$this->language->lang('STATUS')] .= ' » <a href="' . append_sid('ticket.php?ticket_id=' . $ticket['duplicate_id']) . '" style="color: #ff0000">#' . $ticket['duplicate_id'] . '</a>';
		}

		$ticket_details[$this->language->lang('SEVERITY')] = (!empty($ticket['severity_name'])) ? $ticket['severity_name'] : $this->language->lang('UNCATEGORISED');
		$ticket_details[$this->language->lang('COMPONENT')] = (!empty($ticket['component_name'])) ? $ticket['component_name'] : $this->language->lang('UNKNOWN');

		$reported_by = $this->get_user_data($ticket['reporter_id']);
		$send_pm = append_sid("{$this->root_path}ucp.{$this->php_ext}", 'i=pm&amp;mode=compose&amp;u=' . $ticket['reporter_id']);
		$reported_by .= ' (<a href="' . $send_pm . '">Send PM</a>)';

		$ticket_details[$this->language->lang('ASSIGNED_TO')] = (!empty($ticket['assigned'])) ? $ticket['assigned'] : $this->language->lang('UNASSIGNED');

		$ticket_details[$this->language->lang('REPORTED_BY')] = $reported_by;

		// Show reporter's IP address to help with log files
		if (!empty($ticket['reporter_ip']) && $tracker['tracker_name'] == 'incidents' && $this->is_team_user())
		{
			$ticket_details[$this->language->lang('REPORTED_FROM')] = $ticket['reporter_ip'];
		}

		$ticket_details[$this->language->lang('REPORTED_ON')] = $this->user->format_date($ticket['timestamp_created']);

		// Export available ticket details to the template
		foreach ($ticket_details as $detail_name => $detail_value)
		{
			$this->template->assign_block_vars('ticket_details', array(
				'NAME'	=> $detail_name,
				'VALUE'	=> $detail_value,
			));
		}
	}

	public function get_posts_history($ticket, $start)
	{
		// Grab the posts
		$sql = 'SELECT p.*, u.user_id, r.rank_title
			FROM (' . $this->table_prefix . tables::TRACKERS_POST . ' p, ' . USERS_TABLE . ' u)
			LEFT JOIN ' . RANKS_TABLE . ' r
				ON r.rank_id = u.user_rank
			WHERE p.user_id = u.user_id
				AND p.post_id <> ' . $ticket['post_id'] . '
				AND p.ticket_id = ' . (int) $ticket['ticket_id'];

		if (!$this->is_team_user())
		{
			$sql .= ' AND p.post_private = 0';
		}

		$sql .= ' GROUP BY p.post_id
			ORDER BY p.post_timestamp';
		$posts_result = $this->db->sql_query_limit($sql, $this->config['posts_per_page'] + 1, $start);
		$posts = array();
		while ($post_data = $this->db->sql_fetchrow($posts_result))
		{
			$posts[] = $post_data;
		}
		$this->db->sql_freeresult($posts_result);

		$post_min_timestamp = 0;
		$post_max_timestamp = PHP_INT_MAX;

		if (sizeof($posts))
		{
			$post_min_timestamp = $posts[0]['post_timestamp'];
			$post_max_timestamp = $posts[sizeof($posts) - 1]['post_timestamp'];
		}

		if (sizeof($posts) == $this->config['posts_per_page'] + 1)
		{
			array_pop($posts);
		}
		else
		{
			$post_max_timestamp = PHP_INT_MAX;
		}

		if (!$start)
		{
			$post_min_timestamp = 0;
		}

		$history_type = $this->get_history_type();

		// Get the total number of history entries for this ticket
		$sql = 'SELECT COUNT(history_id) AS history_total
			FROM ' . $this->table_prefix . tables::TRACKERS_HISTORY . '
			WHERE ticket_id = ' . (int) $ticket['ticket_id'] . '
				AND history_type <= ' . $history_type;
		$result = $this->db->sql_query($sql);
		$history_total = $this->db->sql_fetchfield('history_total', 0, $result);
		$this->db->sql_freeresult($result);

		// Get this ticket's history
		$sql = 'SELECT h.*, u.user_id, r.rank_title
			FROM ' . $this->table_prefix . tables::TRACKERS_HISTORY . ' h, ' . USERS_TABLE . ' u
			LEFT JOIN ' . RANKS_TABLE . ' r
				ON r.rank_id = u.user_rank
			WHERE h.ticket_id = ' . (int) $ticket['ticket_id'] . '
				AND h.user_id = u.user_id
				AND h.history_timestamp < ' . (int) $post_max_timestamp . '
				AND h.history_timestamp >= ' . (int) $post_min_timestamp . '
				AND h.history_type <= ' . $history_type . '
			ORDER BY h.history_timestamp ASC';
		$history_result = $this->db->sql_query($sql);
		$history_entries = array();
		while ($history_data = $this->db->sql_fetchrow($history_result))
		{
			$history_entries[] = $history_data;
		}
		$this->db->sql_freeresult($history_result);

		$post_data = $history_data = false;

		while (sizeof($posts) || sizeof($history_entries) || $post_data || $history_data)
		{
			if (!$post_data)
			{
				$post_data = array_shift($posts);
			}

			if (!$history_data)
			{
				$history_data = array_shift($history_entries);
			}

			if (!$history_data || ($post_data && ($post_data['post_timestamp'] < $history_data['history_timestamp'])))
			{
				$post_text = generate_text_for_display($post_data['post_text'], $post_data['bbcode_uid'], $post_data['bbcode_bitfield'], $post_data['bbcode_flags']);

				$this->template->assign_block_vars('ticket_posts', array(
					'S_TYPE'	=> 'POST',

					'U_EDIT'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'posting', 'mode' => 'edit', 'post' => (int) $post_data['post_id'])),
					'U_DELETE'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'posting', 'mode' => 'delete', 'post' => (int) $post_data['post_id'])),
					'U_INFO'	=> '',
					'U_QUOTE'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'posting', 'mode' => 'reply')),

					'ID'		=> $post_data['post_id'],
					'TEXT'		=> censor_text($post_text),
					'USER'		=> $this->get_user_data($post_data['user_id']),
					'USER_RANK'	=> $post_data['rank_title'],
					'TIMESTAMP'	=> $this->user->format_date($post_data['post_timestamp']),
				));

				$post_data = false;
			}
			else
			{
				$this->template->assign_block_vars('ticket_posts', array(
					'S_TYPE'	=> 'HISTORY',
					'ID'		=> $history_data['history_id'],
					'USER'		=> $this->get_user_data($history_data['user_id']),
					'USER_RANK'	=> $history_data['rank_title'],
					'TEXT'		=> censor_text($history_data['history_text']),
					'TIMESTAMP'	=> $this->user->format_date($history_data['history_timestamp']),
				));

				$history_data = false;
			}
		}
	}

	public function get_history_type($project_id = 0)
	{
		if (empty($this->user->data) || empty($this->user->data['is_registered']) || !empty($this->user->data['is_bot']))
		{
			return constants::TYPE_PUBLIC;
		}

		switch (true)
		{
			case ($this->is_team_user()):
				return constants::TYPE_TEAM;

			default:
				return constants::TYPE_PUBLIC;
		}
	}

	public function get_group_data($id)
	{
		$group_helper = $this->container->get('group_helper');

		$sql = 'SELECT *
			FROM ' . GROUPS_TABLE . '
			WHERE group_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$group_data = '';
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
			$group_name = $group_helper->get_name($row['group_name']);

			if ($row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile')))
			{
				$group_data = '<span' . $colour_text . '>' . $group_name . '</span>';
			}
			else
			{
				$group_data = '<a' . $colour_text . ' href="' . generate_board_url() . '/' . append_sid("memberlist.$this->php_ext", 'mode=group&amp;g=' . $row['group_id']) . '"><span style="font-weight: bold;">' . $group_name . '</span></a>';
			}
		}
		$this->db->sql_freeresult($result);

		return $group_data;
	}

	public function get_user_data($id)
	{
		$sql = 'SELECT username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$user_data = '';
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['user_colour']) ? ' style="color:#' . $row['user_colour'] . '"' : '';
			if ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'))
			{
				$user_data = '<span' . $colour_text . '>' . $row['username'] . '</span>';
			}
			else
			{
				$user_data = get_username_string('full', $id, $row['username'], $row['user_colour']);
			}
		}
		$this->db->sql_freeresult($result);

		return $user_data;
	}

	public function is_team_user($user_id = 0)
	{
		if (empty($user_id))
		{
			$user_id = (int) $this->user->data['user_id'];

			if ($user->data['is_bot'] || $user_id == ANONYMOUS)
			{
				return false;
			}
		}

		return array_key_exists($user_id, $this->get_team_users());
	}

	public function get_team_users($project_id = 0)
	{
		$sql_where = '';
		$users = $auth_groups = array();

		if (!empty($project_id))
		{
			$sql_where = ' AND pa.project_id = ' . (int) $project_id;
		}

		// Get the users
		$sql = 'SELECT u.user_id
			FROM ' . $this->table_prefix . tables::TRACKERS_PROJECT_AUTH . ' pa, ' . USERS_TABLE . ' u
			WHERE u.user_id = pa.user_id' . $sql_where;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$users[] = $row['user_id'];
		}
		$this->db->sql_freeresult($result);

		// Group query
		$sql = 'SELECT group_id
			FROM ' . $this->table_prefix . tables::TRACKERS_PROJECT_AUTH;

		if (!empty($project_id))
		{
			$sql .= ' WHERE project_id = ' . (int) $project_id;
		}

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$auth_groups[] = $row['group_id'];
		}
		$this->db->sql_freeresult($result);

		if (!empty($auth_groups))
		{
			$sql = 'SELECT u.user_id
				FROM ' . USERS_TABLE . ' u, ' . USER_GROUP_TABLE . ' ug
				WHERE u.user_id = ug.user_id
					AND ug.user_pending = 0
					AND ' . $this->db->sql_in_set('ug.group_id', $auth_groups) . '
				GROUP BY u.user_id';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$users[] = $row['user_id'];
			}
			$this->db->sql_freeresult($result);
		}

		$users = array_unique($users);

		return $users;
	}

	public function generate_navlinks($navlinks)
	{
		if ($navlinks)
		{
			foreach ($navlinks as $navlink)
			{
				$this->template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $navlink['FORUM_NAME'],
					'U_VIEW_FORUM'	=> $navlink['U_VIEW_FORUM'],
				));
			}
		}
	}
}
