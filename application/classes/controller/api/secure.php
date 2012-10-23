<?php

abstract class Controller_Api_Secure extends RESTful_Controller
{
	public $user;
	
	public function before()
	{
		parent::before();

		if(Auth::instance()->logged_in()) {
			$this->user = Auth::instance()->get_user();
		} else {
			$request_body = $this->request->body();
			$key = $this->request->query('key');
			if(empty($key) && !empty($request_body['key']))
				$key = $request_body['key'];
			$username = $this->request->query('user');
			if(empty($username) && !empty($request_body['user']))
				$username = $request_body['user'];

			$request = $this->request;
			$user = ORM::factory('user')
					->where('api_key','=',$key)
					->and_where('username','=',$username)
					->find();
			if ($user->loaded() && is_numeric($user->id)) {
				Auth::instance()->force_login($user);
				$this->user = Auth::instance()->get_user();
			} else {
				throw new HTTP_Exception_403();
			}
		}
	}
}
