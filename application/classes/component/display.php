<?php
class Component_Display
{
	public static function Navbar()
	{
		return View::factory('component/display/navbar');
	}

	public static function Sidebar()
	{
		$labels = Dispatch::factory('api/labels')->find();
		if($labels->loaded()) {
			return View::factory('component/display/sidebar')
					->set('master_labels', $labels['master'])
					->set('user_labels', $labels['user']);
		}
		return '';
	}
}
