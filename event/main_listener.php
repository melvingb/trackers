<?php
/**
 *
 * Trackers extension for the phpBB Forum Software package
 *
 * @copyright (c) 2020, kinerity, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\trackers\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Trackers event listener
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var array */
	protected $tables;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\controller\helper           $helper
	 * @param \phpbb\template\template           $template
	 * @param array                              $tables
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\template\template $template, $tables)
	{
		$this->db = $db;
		$this->language = $language;
		$this->helper = $helper;
		$this->template = $template;
		$this->tables = $tables;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'	=> 'load_language_on_setup',
			'core.page_header'	=> 'add_page_header_link',
			'core.permissions'	=> 'add_permissions',
		];
	}

	/**
	 * Load common language files during user setup
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'kinerity/trackers',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add a link to the controller in the forum navbar
	 */
	public function add_page_header_link()
	{
		$sql = 'SELECT tracker_id, tracker_name
			FROM ' . $this->tables['trackers_tracker'];
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('trackers', [
				'TRACKER_NAME'	=> $row['tracker_name'],
				'U_VIEWTRACKER'	=> $this->helper->route('kinerity_trackers_controller', ['page' => 'viewtracker', 't' => (int) $row['tracker_id']]),
			]);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Add permissions to the ACP -> Permissions settings page
	 * This is where permissions are assigned language keys and
	 * categories (where they will appear in the Permissions table):
	 * actions|content|forums|misc|permissions|pm|polls|post
	 * post_actions|posting|profile|settings|topic_actions|user_group
	 *
	 * Developers note: To control access to ACP, MCP and UCP modules, you
	 * must assign your permissions in your module_info.php file. For example,
	 * to allow only users with the a_trackers permission access to your
	 * ACP module, you would set this in your acp/main_info.php:
	 *    'auth' => 'ext_kinerity/trackers && acl_a_trackers'
	 */
	public function add_permissions($event)
	{
		$permissions = $event['permissions'];
		//$categories = $event['categories'];

		//$categories['trackers'] = 'ACL_CAT_TRACKERS';
		$event->update_subarray('categories', 'trackers', 'ACL_CAT_TRACKERS');

		$permissions['u_tracker_post'] = ['lang' => 'ACL_U_TRACKER_POST', 'cat' => 'trackers'];
		$permissions['u_tracker_edit'] = ['lang' => 'ACL_U_TRACKER_EDIT', 'cat' => 'trackers'];
		$permissions['u_tracker_delete'] = ['lang' => 'ACL_U_TRACKER_DELETE', 'cat' => 'trackers'];
		$permissions['u_tracker_reply'] = ['lang' => 'ACL_U_TRACKER_REPLY', 'cat' => 'trackers'];

		$permissions['m_tracker_edit'] = ['lang' => 'ACL_M_TRACKER_EDIT', 'cat' => 'trackers'];
		$permissions['m_tracker_delete'] = ['lang' => 'ACL_M_TRACKER_DELETE', 'cat' => 'trackers'];

		//$event['categories'] = $categories;
		$event['permissions'] = $permissions;
	}
}
