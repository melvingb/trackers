<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2019, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\trackers\event;

use kinerity\trackers\tables;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Trackers event listener
 */
class main_listener implements EventSubscriberInterface
{
	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\template\template           $template
	 * @param string                             $table_prefix
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\template\template $template, $table_prefix)
	{
		$this->db = $db;
		$this->helper = $helper;
		$this->template = $template;
		$this->table_prefix = $table_prefix;
	}

	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'	=> 'load_language_on_setup',
			'core.permissions'	=> 'add_permission',
			'core.page_header'	=> 'add_page_header_link',
		);
	}

	/**
	 * Load common language files during user setup
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'kinerity/trackers',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add permissions for Trackers
	 */
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$categories = $event['categories'];

		$categories['trackers'] = 'ACL_CAT_TRACKERS';

		$permissions['u_trackers_attach'] = array('lang' => 'ACL_U_TRACKERS_ATTACH', 'cat' => 'trackers');
		$permissions['u_trackers_post'] = array('lang' => 'ACL_U_TRACKERS_POST', 'cat' => 'trackers');
		$permissions['u_trackers_reply'] = array('lang' => 'ACL_U_TRACKERS_REPLY', 'cat' => 'trackers');
		$permissions['u_trackers_edit'] = array('lang' => 'ACL_U_TRACKERS_EDIT', 'cat' => 'trackers');
		$permissions['u_trackers_delete'] = array('lang' => 'ACL_U_TRACKERS_DELETE', 'cat' => 'trackers');

		$permissions['m_trackers_edit'] = array('lang' => 'ACL_M_TRACKERS_EDIT', 'cat' => 'trackers');
		$permissions['m_trackers_delete'] = array('lang' => 'ACL_M_TRACKERS_DELETE', 'cat' => 'trackers');
		$permissions['m_trackers_lock'] = array('lang' => 'ACL_M_TRACKERS_LOCK', 'cat' => 'trackers');
		$permissions['m_trackers_move'] = array('lang' => 'ACL_M_TRACKERS_MOVE', 'cat' => 'trackers');
		$permissions['m_trackers_assign'] = array('lang' => 'ACL_M_TRACKERS_ASSIGN', 'cat' => 'trackers');
		$permissions['m_trackers_chgstatus'] = array('lang' => 'ACL_M_TRACKERS_CHGSTATUS', 'cat' => 'trackers');
		$permissions['m_trackers_chgpriority'] = array('lang' => 'ACL_M_TRACKERS_CHGPRIORITY', 'cat' => 'trackers');
		$permissions['m_trackers_chgseverity'] = array('lang' => 'ACL_M_TRACKERS_CHGSEVERITY', 'cat' => 'trackers');

		$permissions['a_trackers_manage'] = array('lang' => 'ACL_A_TRACKERS_MANAGE', 'cat' => 'trackers');

		$event['categories'] = $categories;
		$event['permissions'] = $permissions;
	}

	/**
	 * Add a link to the controller in the forum navbar
	 */
	public function add_page_header_link()
	{
		$sql = 'SELECT tracker_id, tracker_name
			FROM ' . $this->table_prefix . tables::TRACKERS_TRACKER . '
			WHERE tracker_active = ' . 1 . '
			ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('trackers', array(
				'TRACKER_NAME'	=> $row['tracker_name'],

				'U_VIEWTRACKER'	=> $this->helper->route('kinerity_trackers_controller', array('page' => 'viewtracker', 't' => (int) $row['tracker_id'])),
			));
		}
		$this->db->sql_freeresult($result);
	}
}
