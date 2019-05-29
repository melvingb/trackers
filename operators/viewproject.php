<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2019, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\trackers\operators;

use Symfony\Component\DependencyInjection\ContainerInterface;

class viewproject
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var ContainerInterface */
	protected $container;

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

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config               $config
	 * @param ContainerInterface                 $container
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\request\request             $request
	 * @param \phpbb\template\template           $template
	 * @param \phpbb\user                        $user
	 */
	public function __construct(\phpbb\config\config $config, ContainerInterface $container, \phpbb\controller\helper $helper, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->config = $config;
		$this->container = $container;
		$this->helper = $helper;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user; // Not used?
	}

	public function display()
	{
		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);

		$start = $this->request->variable('start', 0);
		$ticket_status = $this->request->variable('ticket_status', 0);

		$pagination = $this->container->get('pagination');

		$s_hidden_fields = build_hidden_fields(array('t' => (int) $tracker_id, 'p' => (int) $project_id));

		$tracker = $this->container->get('kinerity.trackers.functions.tracker')->get_tracker_data($tracker_id);
		$project = $this->container->get('kinerity.trackers.functions.tracker')->get_project_data($project_id);
		$statuses = $this->container->get('kinerity.trackers.functions.tracker')->get_statuses($tracker_id);

		foreach ($statuses as $status_id => $status_data)
		{
			$this->template->assign_block_vars('statuses', array(
				'ID'	=> $status_id,
				'NAME'	=> $status_data['status_name'],
			));

			if ($status_data['ticket_new'])
			{
				$status_new = $status_id;
			}
		}

		$sql = $this->container->get('kinerity.trackers.functions.tracker')->tickets_sql($tracker, $project_id, $ticket_status);

		$total_tickets = $this->container->get('kinerity.trackers.functions.tracker')->get_total_tickets($sql);

		// Handle pagination
		$start = $pagination->validate_start($start, $this->config['tickets_per_page'], $total_tickets);

		$base_url = $this->helper->route('kinerity_trackers_controller', array('page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket_status' => (int) $ticket_status));
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_tickets, $this->config['tickets_per_page'], $start);

		$this->container->get('kinerity.trackers.functions.tracker')->get_tickets($sql, $start, $status_new);

		switch ($ticket_status)
		{
			case 0:
				$status_name = $this->language->lang('ALL_OPEN');
			break;

			case -1:
				$status_name = $this->language->lang('ALL_TICKETS');
			break;

			case -2:
				$status_name = $this->language->lang('ALL_CLOSED');
			break;

			default:
				$status = $this->container->get('kinerity.trackers.functions.tracker')->get_status_data($ticket_status);
				$status_name = $status['status_name'];
			break;
		}

		$this->template->assign_vars(array(
			'TRACKER_NAME'	=> $tracker['tracker_name'],

			'STATUS_ID'	=> $ticket_status,
			'STATUS'	=> $status_name,

			'TOTAL_TICKETS'	=> $this->language->lang('TOTAL_TICKETS', $total_tickets),

			'U_ACTION'			=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id)),
			'U_POST_NEW_TICKET'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'posting', 'mode' => 'post', 't' => (int) $tracker_id, 'p' => (int) $project_id)),

			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
		));

		$navlinks = array(
			array(
				'FORUM_NAME'	=> $tracker['tracker_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewtracker', 't' => (int) $tracker_id)),
			),

			array(
				'FORUM_NAME'	=> $project['project_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id)),
			),
		);

		$this->container->get('kinerity.trackers.functions.tracker')->generate_navlinks($navlinks);

		return $this->helper->render('viewproject_body.html', $tracker['tracker_name']);
	}
}
