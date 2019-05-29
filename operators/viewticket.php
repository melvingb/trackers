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
use kinerity\trackers\tables;
use kinerity\trackers\constants;

class viewticket
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
		$this->user = $user;//
	}

	public function display()
	{
		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);
		$ticket_id = $this->request->variable('ticket', 0);

		$start = $this->request->variable('start', 0);

		$pagination = $this->container->get('pagination');

		$tracker = $this->container->get('kinerity.trackers.functions.tracker')->get_tracker_data($tracker_id);
		$project = $this->container->get('kinerity.trackers.functions.tracker')->get_project_data($project_id);
		$ticket = $this->container->get('kinerity.trackers.functions.tracker')->get_ticket_data($ticket_id);

		$this->container->get('kinerity.trackers.functions.tracker')->get_ticket_details($project, $ticket);

		$total_posts = $this->container->get('kinerity.trackers.functions.tracker')->get_total_posts($ticket_id);

		// Handle pagination
		$start = $pagination->validate_start($start, $this->config['posts_per_page'], $total_posts);

		$base_url = $this->helper->route('kinerity_trackers_controller', array('page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id));
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_posts, $this->config['posts_per_page'], $start);

		$post_text = $this->container->get('kinerity.trackers.functions.tracker')->get_ticket_text($ticket_id);

		$this->template->assign_vars(array(
			'TRACKER_NAME'	=> $tracker['tracker_name'],
			'TICKET_TITLE'	=> $ticket['ticket_title'],
			'STATUS_NAME'	=> $ticket['status_name'],
			'TICKET_TEXT'	=> $post_text,
			'TICKET_ID'		=> $ticket_id,

			'TOTAL_POSTS'	=> $this->language->lang('PAGE_TOTAL_POSTS', $total_posts),

			'S_CLOSED'			=> false,
			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
		));

		$this->container->get('kinerity.trackers.functions.tracker')->get_posts_history($ticket, $start);

		$navlinks = array(
			array(
				'FORUM_NAME'	=> $tracker['tracker_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewtracker', 't' => (int) $tracker_id)),
			),

			array(
				'FORUM_NAME'	=> $project['project_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id)),
			),

			array(
				'FORUM_NAME'	=> $ticket['ticket_title'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id)),
			),
		);

		$this->container->get('kinerity.trackers.functions.tracker')->generate_navlinks($navlinks);

		return $this->helper->render('viewticket_body.html', $tracker['tracker_name']);
	}
}
