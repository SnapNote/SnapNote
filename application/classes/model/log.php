<?php
class Model_Log extends ORM
{
	public static $class_name = 'log';
	public static $table_name = 'log';
	protected $_table_name = 'log';

	protected $_belongs_to = array(
		'note' => array(
			'model' => 'note',
			'foreign_key' => 'note_id'
		),
		'note_version' => array(
			'model' => 'note_version',
			'foreign_key' => 'note_version_id'
		),
		'label' => array(
			'model' => 'label',
			'foreign_key' => 'label_id'
		),
		'user' => array(
			'model' => 'user',
			'foreign_key' => 'user_id',
		),
	);
	
	public function rules()
    {
        return array(
            'event' => array(
                array('not_empty'),
            ),
        );
    }

	public function save(Validation $validation = NULL)
    {
        if (!$this->pk() || isset($this->_changed[$this->_primary_key])) {
        	$this->created = date('Y-m-d H:i:s');
        }
        return parent::save($validation);
    }
}
