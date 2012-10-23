<?php
class Model_Note_Version extends ORM
{
	public static $class_name = 'note_version';
	public static $table_name = 'note_versions';
	protected $_table_name = 'note_versions';

	protected $_belongs_to = array(
		'note' => array(
			'model' => 'note',
			'foreign_key' => 'note_id'
		),
		'user' => array(
			'model' => 'user',
			'foreign_key' => 'user_id',
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

	public function save(Validation $validation = NULL)
    {
        if (!$this->pk() || isset($this->_changed[$this->_primary_key])) {
        	$this->created = date('Y-m-d H:i:s');
        }
       	$this->modified = date('Y-m-d H:i:s');
        return parent::save($validation);
    }
}
