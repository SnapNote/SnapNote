<?php
class Model_User extends Useradmin_Model_User
{
	protected $_has_many = array(
		// auth
		'roles' => array('through' => 'roles_users'),
		'user_tokens' => array(),
		// for facebook / twitter / google / yahoo identities
		'user_identity' => array(),
		'notes' => array(
			'model' => 'note_user',
			'foreign_key' => 'user_id'
		),
		'labels' => array(
			'model' => 'label',
			'foreign_key' => 'user_id'
		),
	);
	
	public function rules()
	{
		$parent = parent::rules();
		// fixes the min_length username value
		$parent['username'][1] = array('min_length', array(':value', 1));
		$parent['name'][] = array('not_empty');
	    $parent['email'][] = array('email');
		return $parent;
	}

	public static function getList()
	{
		$results = array();
		foreach(DB::select()
				->from('users')
				->order_by('users.name')
				->execute() as $entry)
			$results[$entry['id']] = $entry['name'];
		return $results;
	}
	
	public static function getListByRole($role)
	{
		$results = array();
		foreach(DB::select('users.*')
				->from('users')
				->join('roles_users','left')->on('roles_users.user_id','=','users.id')
				->join('roles','left')->on('roles_users.role_id','=','roles.id')
				->where('roles.name','=',$role)
				->order_by('users.name')
				->execute() as $entry)
			$results[$entry['id']] = $entry['name'];
		return $results;
	}
	
	public static function getEmail($user_id)
	{
		return DB::select('email')
				->from('users')
				->where('id','=',$user_id)
				->execute()
				->get('email');
	}

	public function notes()
	{
		$notes = array();
		foreach($this->notes
			->find_all() as $note)
			$notes[$note->note->id] = array(
				'id' => $note->note->id,
				'subject' => $note->note->subject,
			);
		return $notes;
	}
}
