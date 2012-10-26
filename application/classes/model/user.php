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
		$parent['name'][] = array('min_length', array(':value', 1));;
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

	public function notes($search = null)
	{
		$notes = array();
		$find_notes = $this->notes;
		if(!empty($search)) {
			$search = explode(' ', $search);
			$find_notes->with('note');
			foreach($search as $keyword)
				$find_notes->and_where('note.subject','like',"%{$keyword}%");
		}
		foreach($find_notes->find_all() as $note) {
			$note_labels = $note->note->noteLabels($this->id);
			if($this->id == Model_Label::LABEL_TRASH || !array_key_exists(Model_Label::LABEL_TRASH, $note_labels)) {
				$notes[$note->note->id] = array(
					'id' => $note->note->id,
					'subject' => $note->note->subject,
					'labels' => $note_labels,
					'modified' => $note->note->modified,
				);
			}
		}
		return $notes;
	}
}
