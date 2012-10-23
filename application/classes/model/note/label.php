<?php
class Model_Note_Label extends ORM
{
	public static $class_name = 'note_label';
	public static $table_name = 'note_labels';
	protected $_table_name = 'note_labels';

	protected $_belongs_to = array(
		'note' => array(
			'model' => 'note',
			'foreign_key' => 'note_id'
		),
		'label' => array(
			'model' => 'label',
			'foreign_key' => 'label_id',
		),
		'user' => array(
			'model' => 'user',
			'foreign_key' => 'user_id',
		),
	);
	
	public function rules()
    {
        return array(
            'note_id' => array(
                array('not_empty'),
            ),
            'label_id' => array(
                array('not_empty'),
            ),
            'user_id' => array(
                array('not_empty'),
            ),
        );
    }
}
