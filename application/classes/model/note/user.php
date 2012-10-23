<?php
class Model_Note_User extends ORM
{
	public static $class_name = 'note_user';
	public static $table_name = 'note_users';
	protected $_table_name = 'note_users';

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
            'permission' => array(
                array('not_empty'),
            ),
        );
    }
}
