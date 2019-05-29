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

/**
 * Trackers ACP module info
 */
class trackers_info
{
	public function module()
	{
		return array(
			'filename'	=> '\kinerity\trackers\acp\trackers_module',
			'title'		=> 'TRACKERS',
			'modes'		=> array(
				'manage'	=> array(
					'title'	=> 'ACP_MANAGE_TRACKERS',
					'auth'	=> 'ext_kinerity/trackers && acl_a_trackers_manage',
					'cat'	=> array('TRACKERS')
				),
			),
		);
	}
}
