<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2020, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\trackers\operators;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Statistics operator
 */
class statistics
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var array */
	protected $tables;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config               $config
	 * @param ContainerInterface                 $container
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\request\request             $request
	 * @param \phpbb\template\template           $template
	 * @param \phpbb\user                        $user
	 * @param array                              $tables
	 */
	public function __construct(\phpbb\config\config $config, ContainerInterface $container, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $tables)
	{
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->language = $language;
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->tables = $tables;
	}

	public function display()
	{
		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);

		$tracker = $this->container->get('kinerity.trackers.functions')->get_tracker_data($tracker_id);

		if (!$tracker['allow_view_all'] && !$this->container->get('kinerity.trackers.functions')->is_team_user())
		{
			throw new \phpbb\exception\http_exception(403, $this->language->lang('NOT_AUTHORISED'));
		}

		$timespan_start = $this->request->variable('start', 0);
		$timespan_end = $this->request->variable('end', 0);

		// Current month
		if ($timespan_start == 0)
		{
			$timespan_start = (int) mktime(0, 0, 0, date('n'), 1);
		}

		if ($timespan_start == -1)
		{
			$timespan_start = 0;
		}

		// Use $timespan_start + 1 month as $timespan_end
		if ($timespan_end == 0)
		{
			$timespan_end = (int) mktime(0, 0, 0, date('n', $timespan_start) + 1, 1, date('Y', $timespan_start));
		}
		else if ($timespan_end == -1)
		{
			$timespan_end = time();
		}

		$this->template->assign_vars([
			'TRACKER_NAME'	=> $tracker['tracker_name'],
		]);

		$navlinks = [
			[
				'FORUM_NAME'	=> $tracker['tracker_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'viewtracker', 't' => (int) $tracker_id]),
			],
		];

		$this->container->get('kinerity.trackers.functions')->generate_navlinks($navlinks);

		// Tracker statistics
		if (!$project_id)
		{
			$sql = 'SELECT tracker_id, tracker_name
				FROM ' . $this->tables['trackers_tracker'];
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('trackers_stats', [
					'TRACKER_NAME'		=> $row['tracker_name'],
					'U_TRACKER_STATS'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'statistics', 't' => (int) $row['tracker_id']]),
				]);
			}
			$this->db->sql_freeresult($result);

			$this->template->assign_vars([
				'TIMESPAN_TICKETS'	=> $this->language->lang('TIMESPAN_TICKETS', $this->user->format_date($timespan_start, 'F jS, Y'), $this->user->format_date($timespan_end, 'F jS, Y')),

				'STATISTICS_EXPLAIN'	=> $this->language->lang('STATISTICS_TRACKER_EXPLAIN', $tracker['tracker_name'], $this->config['sitename']),
			]);

			// Current month (or other timestamp)
			$this->container->get('kinerity.trackers.functions')->generate_stats('projects', $timespan_start, $timespan_end, $tracker_id);

			// Totals
			$this->container->get('kinerity.trackers.functions')->generate_stats('projects_total', 0, 0, $tracker_id);

			return $this->helper->render('statistics_tracker_body.html', $tracker['tracker_name']);
		}
		// Project statistics
		else
		{
			$project = $this->container->get('kinerity.trackers.functions')->get_project_data($project_id);

			$sql_where = 'project_id = ' . (int) $project_id . '
				AND timestamp_created BETWEEN ' . (int) $timespan_start . ' AND ' . (int) $timespan_end;

			if ($timespan_start > 0 && $timespan_end > 0)
			{
				$search_filter = $this->language->lang('FILTER_BETWEEN', $this->user->format_date($timespan_start, 'F jS, Y'), $this->user->format_date($timespan_end, 'F jS, Y'));
			}
			else if ($timespan_start > 0)
			{
				$search_filter = $this->language->lang('FILTER_AFTER', $this->user->format_date($timespan_start, 'F jS, Y'));
			}
			else if ($timespan_end > 0)
			{
				$search_filter = $this->language->lang('FILTER_BEFORE', $this->user->format_date($timespan_end, 'F jS, Y'));
			}

			// Get status_id -> number of tickets
			$status_ids = [];
			$statuses = $this->container->get('kinerity.trackers.functions')->get_status($tracker_id);

			foreach ($statuses as $status)
			{
				$status_ids[] = $status['status_id'];
			}

			$sql = 'SELECT status_id, COUNT(ticket_id) AS tickets_count
				FROM ' . $this->tables['trackers_ticket'] . '
				WHERE ' . $this->db->sql_in_set('status_id', $status_ids) . '
					AND ' . $sql_where . '
				GROUP BY status_id';
			$result = $this->db->sql_query($sql);
			$tickets_count = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$tickets_count[$row['status_id']] = $row['tickets_count'];
			}
			$this->db->sql_freeresult($result);

			foreach ($statuses as $status)
			{
				$status_tickets = (isset($tickets_count[$status['status_id']])) ? $tickets_count[$status['status_id']] : 0;

				$this->template->assign_block_vars('statuses', [
					'S_CLOSED'	=> $status['ticket_closed'],
					'NAME'		=> $status['status_name'],
					'TICKETS'	=> $status_tickets,
				]);
			}

			$this->template->assign_vars([
				'STATISTICS_EXPLAIN'	=> $this->language->lang('STATISTICS_PROJECT_EXPLAIN', $project['project_name'], $this->config['sitename'], $tracker['tracker_name']),
				'SEARCH_FILTER'	=> $search_filter,

				'U_TRACKER_STATS'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'statistics', 't' => (int) $tracker_id]),
			]);

			return $this->helper->render('statistics_project_body.html', $project['project_name']);
		}
	}
}
