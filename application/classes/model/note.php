<?php
class Model_Note extends ORM
{
	public static $class_name = 'note';
	public static $table_name = 'notes';
	protected $_table_name = 'notes';

	protected $_belongs_to = array(
		'category' => array(
			'model' => 'file_category',
			'foreign_key' => 'file_category_id'
		),
		'user' => array(
			'model' => 'user',
			'foreign_key' => 'user_id',
		),
	);

	protected $_has_many = array(
		'note_users' => array(
			'model' => 'note_user',
			'foreign_key' => 'note_id',
		),
		'versions' => array(
			'model' => 'note_version',
			'foreign_key' => 'note_id',
		),
		'labels' => array(
			'model' => 'note_label',
			'foreign_key' => 'note_id',
		),
	);

	public function rules()
    {
        return array(
            'subject' => array(
                array('not_empty'),
            ),
        );
    }
	
	public function noteLabels($user_id)
	{
		$labels = array();
		foreach($this->labels
			->where('user_id','=',$user_id)
			->find_all() as $label)
			$labels[$label->label->id] = array(
				'id' => $label->label->id,
				'name' => $label->label->name,
			);
		return $labels;
	}

	public function addLabel($label_id, $user_id)
	{
		$label = ORM::factory('label', $label_id);
		if($label->loaded()) {
			$current_label = $this->labels->where('label_id','=',$label_id)->find();
			if(!$current_label->loaded()) {
				$note_label = ORM::factory('note_label');
				$note_label->note_id = $this->id;
				$note_label->label_id = $label_id;
				$note_label->user_id = $user_id;
				$note_label->save();
			}
		}
	}

	public function removeLabel($label_id, $user_id)
	{
		$current_label = $this->labels
			->where('label_id','=',$label_id)
			->and_where('user_id','=',$user_id)
			->find();
		if($current_label->loaded()) {
			$current_label->delete();
		}
	}
	
	public function deactivateVersions()
	{
		foreach($this->versions->find_all() as $version) {
			$version->status = 'inactive';
			$version->save();
		}
	}

	public function save(Validation $validation = NULL)
    {
        if (!$this->pk() || isset($this->_changed[$this->_primary_key])) {
        	$this->created = date('Y-m-d H:i:s');
        }
        return parent::save($validation);
    }
	
	public function permission($user_id)
	{
		$note_user = $this->note_users->where('user_id','=',$user_id)->find();
		if($note_user->loaded())
			return $note_user->permission;
		return false;
	}
	
	public function can_view($user_id)
	{
		if($this->note_users->where('user_id','=',$user_id)->find()->loaded())
			return true;
		return false;
	}
	
	public function active_version()
	{
		return ORM::factory('note_version', $this->active_note_version_id);
	}
}
