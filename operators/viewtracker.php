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

class viewtracker
{
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
	 * @param ContainerInterface                 $container
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\request\request             $request
	 * @param \phpbb\template\template           $template
	 * @param \phpbb\user                        $user
	 */
	public function __construct(ContainerInterface $container, \phpbb\controller\helper $helper, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->container = $container;
		$this->helper = $helper;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	public function display()
	{
		$tracker_id = $this->request->variable('t', 0);

		$tracker = $this->container->get('kinerity.trackers.functions.tracker')->get_tracker_data($tracker_id);

		if (!$tracker['allow_view_all'] && $this->user->data['user_id'] == ANONYMOUS)
		{
			login_box('', $this->language->lang('LOGIN_REQUIRED'));
		}

		$projects = $this->container->get('kinerity.trackers.functions.tracker')->get_projects($tracker_id);

		foreach ($projects as $project)
		{
			$this->template->assign_block_vars('projectrow', array(
				'PROJECT_NAME'	=> $project['project_name'],
				'DESCRIPTION'	=> $project['project_description'],

				'U_VIEWPROJECT'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewproject', 't' => (int) $tracker_id, 'p' => (int) $project['project_id'])),
			));
		}

		$this->template->assign_vars(array(
			'TRACKER_NAME'	=> $tracker['tracker_name'],

			'U_STATISTICS'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'statistics', 't' => (int) $tracker_id)),

			'S_TRACKER_PRIVATE'	=> !$tracker['allow_view_all'] ? true : false,
		));

		$navlinks = array(
			array(
				'FORUM_NAME'	=> $tracker['tracker_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewtracker', 't' => (int) $tracker_id)),
			),
		);

		$this->container->get('kinerity.trackers.functions.tracker')->generate_navlinks($navlinks);

		return $this->helper->render('viewtracker_body.html', $tracker['tracker_name']);
	}
}
