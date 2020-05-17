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
use kinerity\trackers\constants;

/**
 * Viewticket operator
 */
class viewticket
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface */
	protected $container;

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

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth          $auth
	 * @param \phpbb\config\config      $config
	 * @param ContainerInterface        $container
	 * @param \phpbb\language\language  $language
	 * @param \phpbb\controller\helper  $helper
	 * @param \phpbb\request\request    $request
	 * @param \phpbb\template\template  $template
	 * @param \phpbb\user               $user
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, ContainerInterface $container, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->container = $container;
		$this->language = $language;
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	public function display()
	{
		$tracker_id = $this->request->variable('t', 0);
		$project_id = $this->request->variable('p', 0);
		$ticket_id = $this->request->variable('ticket', 0);

$change_status = $this->request->variable('change_status', 0);
$change_severity = $this->request->variable('change_severity', 0);

// Opps, stopy modifying the URL parameters if you're not authed
if (($change_status || $change_severity) && !$this->container->get('kinerity.trackers.functions')->is_team_user($project_id))
{
	throw new \phpbb\exception\http_exception(403, $this->language->lang('NOT_AUTHORISED'));
}

if ($this->request->is_set_post('change_status'))
{
	$this->container->get('kinerity.trackers.functions')->set_status($ticket_id, $change_status);
}

if ($this->request->is_set_post('change_severity'))
{
	$this->container->get('kinerity.trackers.functions')->set_severity($ticket_id, $change_severity);
}

		$s_hidden_fields = build_hidden_fields(['t' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]);
		$start = $this->request->variable('start', 0);

		$pagination = $this->container->get('pagination');

		$tracker = $this->container->get('kinerity.trackers.functions')->get_tracker_data($tracker_id);
		$project = $this->container->get('kinerity.trackers.functions')->get_project_data($project_id);
		$ticket = $this->container->get('kinerity.trackers.functions')->get_ticket_data($ticket_id);

		$this->container->get('kinerity.trackers.functions')->get_ticket_details($tracker_id, $project, $ticket);

		$total_posts = $this->container->get('kinerity.trackers.functions')->get_total_posts($ticket_id);

		// Handle pagination
		$start = $pagination->validate_start($start, $this->config['posts_per_page'], $total_posts);

		$base_url = $this->helper->route('kinerity_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]);
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_posts, $this->config['posts_per_page'], $start);

		// Get all duplicates of this ticket
		foreach ($this->container->get('kinerity.trackers.functions')->get_duplicate_tickets($ticket_id) as $_ticket_id => $row)
		{
			$this->template->assign_block_vars('duplicates', [
				'ID'		=> $_ticket_id,
				'TITLE'		=> $row['ticket_title'],
				'U_TICKET'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $_ticket_id]),
			]);
		}

		// This ticket is a duplicate of another ticket
		if ($ticket['ticket_duplicate'] && $ticket['duplicate_id'] > 0)
		{
			// Get all duplicates of the duplicate of this ticket
			foreach ($this->container->get('kinerity.trackers.functions')->get_duplicate_tickets($ticket['duplicate_id']) as $_ticket_id => $row)
			{
				$this->template->assign_block_vars('duplicates_other', [
					'ID'		=> $_ticket_id,
					'TITLE'		=> $row['ticket_title'],
					'U_TICKET'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $_ticket_id]),
				]);
			}

			$this->template->assign_var('DUPLICATE_ID', $ticket['duplicate_id']);
		}

		$status = $this->container->get('kinerity.trackers.functions')->get_status($tracker_id);

		foreach ($status as $status_id => $status_data)
		{
			$this->template->assign_block_vars('statuses', [
				'ID'	=> $status_id,
				'NAME'	=> $status_data['status_name'],
			]);
		}

		$severities = $this->container->get('kinerity.trackers.functions')->get_severities($tracker_id);

		foreach ($severities as $severity_id => $severity_data)
		{
			$this->template->assign_block_vars('severities', [
				'ID'	=> $severity_id,
				'NAME'	=> $severity_data['severity_name'],
			]);
		}

		$post_text = $this->container->get('kinerity.trackers.functions')->get_ticket_text($ticket_id);

		$s_edit = $this->user->data['is_registered'] && ($this->auth->acl_get('u_tracker_edit') && $this->user->data['user_id'] == $ticket['user_id']) || ($this->auth->acl_get('m_tracker_edit') && $this->container->get('kinerity.trackers.functions')->is_team_user($project_id));
		$s_delete = $this->user->data['is_registered'] && ($this->auth->acl_get('u_tracker_delete') && $this->user->data['user_id'] == $ticket['user_id']) || ($this->auth->acl_get('m_tracker_delete') && $this->container->get('kinerity.trackers.functions')->is_team_user($project_id));
		$s_quote = ($this->auth->acl_get('m_tracker_edit') && $this->container->get('kinerity.trackers.functions')->is_team_user($project_id)) || ($this->user->data['user_id'] == ANONYMOUS || $this->auth->acl_get('u_tracker_reply'));

		$this->template->assign_vars([
			'STATUS_ID'			=> $ticket['status_id'],
			'SEVERITY_ID'			=> $ticket['severity_id'],
			'S_CLOSED'			=> $ticket['ticket_closed'],
			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
			'S_TICKET_LOCKED'	=> false,
			'S_TICKET_PRIVATE'	=> false,
			'S_TEAM_USER'		=> $this->container->get('kinerity.trackers.functions')->is_team_user($project_id) ? true : false,

			'U_ACTION'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]),
			'U_EDIT'	=> $s_edit ? $this->helper->route('kinerity_trackers_controller', ['page' => 'posting', 'mode' => 'edit', 'post' => (int) $ticket['post_id']]) : '',
			'U_DELETE'	=> $s_delete ? $this->helper->route('kinerity_trackers_controller', ['page' => 'posting', 'mode' => 'delete', 'post' => (int) $ticket['post_id']]) : '',
			'U_QUOTE'	=> $s_quote ? $this->helper->route('kinerity_trackers_controller', ['page' => 'posting', 'mode' => 'reply']) : '',

			'TRACKER_NAME'	=> $tracker['tracker_name'],

			'TICKET_TITLE'	=> $ticket['ticket_title'],
			'TICKET_TEXT'	=> $post_text,
			'STATUS'		=> strtolower($ticket['status_name']),

			'TOTAL_POSTS'	=> $this->language->lang('PAGE_TOTAL_POSTS', $total_posts),
		]);

		$this->container->get('kinerity.trackers.functions')->get_posts_history($ticket, $start);

		$navlinks = [
			[
				'FORUM_NAME'	=> $tracker['tracker_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'viewtracker', 't' => (int) $tracker_id]),
			],

			[
				'FORUM_NAME'	=> $project['project_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project_id]),
			],

			[
				'FORUM_NAME'	=> $ticket['ticket_title'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'viewticket', 't' => (int) $tracker_id, 'p' => (int) $project_id, 'ticket' => (int) $ticket_id]),
			],
		];

		$this->container->get('kinerity.trackers.functions')->generate_navlinks($navlinks);

		return $this->helper->render('viewticket_body.html', $tracker['tracker_name'] . ' - ' . $ticket['ticket_title']);
	}
}
