<?php
class Model_Label extends ORM
{
	public static $class_name = 'label';
	public static $table_name = 'labels';
	protected $_table_name = 'labels';
	
	const LABEL_CURRENT = '1'; 
	const LABEL_STARRED = '2'; 
	const LABEL_TEMPLATES = '3'; 
	const LABEL_TRASH = '4'; 

	protected $_belongs_to = array(
		'parent' => array(
			'model' => 'label',
			'foreign_key' => 'parent_id'
		),
		'user' => array(
			'model' => 'user',
			'foreign_key' => 'user_id',
		),
	);

	protected $_has_many = array(
		'children' => array(
			'model' => 'label',
			'foreign_key' => 'parent_id',
		),
		'notes' => array(
			'model' => 'note_label',
			'foreign_key' => 'label_id',
		),
	);

	public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
            ),
        );
    }

	public function save(Validation $validation = NULL)
    {
		$max_sort_order = $this->max_sort_order();
        if (!$this->pk() || isset($this->_changed[$this->_primary_key])) {
        	$this->created = date('Y-m-d H:i:s');
			if(empty($this->sort_order) || $this->sort_order > ($max_sort_order + 1)) {
				$this->sort_order = ($max_sort_order + 1);
			} else {
				$this->reorder_level();
			}
        } else {
			if(isset($this->_changed['sort_order'])) {
				$this->reorder_level();
			}
		}
        $result = parent::save($validation);
		$this->resort_level();
		return $result;
    }
	
	public function max_sort_order()
	{
		$max_sort_order = ORM::Factory(self::$class_name)
			->select(array(DB::expr('max(sort_order)'),'order_num'))
			->where('parent_id','=',$this->parent_id)
			->and_where('user_id','=',$this->user_id)
			->find();
		if(empty($max_sort_order->order_num))
			return 0;
		return $max_sort_order->order_num;
	}
	
	public function reorder_level()
	{
		$entry = ORM::Factory(self::$class_name, $this->id);
		if($entry->loaded()) {
			if($entry->sort_order < $this->sort_order) {
				DB::update(self::$table_name)
					->set(array('sort_order' => DB::expr('(sort_order - 1)')))
					->where('sort_order','<=',$this->sort_order)
					->and_where('parent_id','=',$this->parent_id)
					->and_where('user_id','=',$this->user_id)
					->execute();
			} else {
				DB::update(self::$table_name)
					->set(array('sort_order' => DB::expr('(sort_order + 1)')))
					->where('sort_order','>=',$this->sort_order)
					->and_where('parent_id','=',$this->parent_id)
					->and_where('user_id','=',$this->user_id)
					->execute();
			}
		} else {
			DB::update(self::$table_name)
				->set(array('sort_order' => DB::expr('(sort_order + 1)')))
				->where('sort_order','>=',$this->sort_order)
				->and_where('parent_id','=',$this->parent_id)
				->and_where('user_id','=',$this->user_id)
				->execute();
		}
	}
	
	public function resort_level()
	{
		$i = 1;
		foreach(ORM::Factory(self::$class_name)
			->where('parent_id','=',$this->parent_id)
			->and_where('user_id','=',$this->user_id)
			->order_by('sort_order')
			->find_all() as $entry) {
			DB::update(self::$table_name)
				->set(array('sort_order' => $i))
				->where('id','=',$entry->id)
				->execute();
			$i++;
		}
	}
	
	public static function masterLabels($user_id)
	{
		$labels = array();
		foreach(ORM::Factory(self::$class_name)
					->where('user_id','=',0)
					->order_by('sort_order')
					->find_all() as $label)
			$labels[$label->id] = array(
				'id' => $label->id,
				'parent_id' => $label->parent_id,
				'sort_order' => $label->sort_order,
				'name' => $label->name,
				'count' => count($label->notes($user_id)),
			);
		$labels = self::buildTree($labels);
		return $labels;
	}

	public static function userLabels($user_id)
	{
		$labels = array();
		foreach(ORM::Factory(self::$class_name)
					->where('user_id','=',$user_id)
					->order_by('sort_order')
					->find_all() as $label)
			$labels[$label->id] = array(
				'id' => $label->id,
				'parent_id' => $label->parent_id,
				'sort_order' => $label->sort_order,
				'name' => $label->name,
				'count' => count($label->notes($user_id)),
			);
		$labels = self::buildTree($labels);
		return $labels;
	}
	
	public static function buildTree(array &$elements, $parentId = 0) {
		$branch = array();
		foreach ($elements as $element) {
			if ($element['parent_id'] == $parentId) {
				$children = self::buildTree($elements, $element['id']);
				if ($children) {
					$element['children'] = $children;
				}
				$branch[$element['id']] = $element;
			}
		}
		return $branch;
	}
	
	public function notes($user_id)
	{
		$notes = array();
		foreach($this->notes
			->with('note')
			->where('user_id','=',$user_id)
			->order_by('note.modified', 'desc')
			->find_all() as $note) {
			$note_labels = $note->note->noteLabels($user_id);
			if($this->id == self::LABEL_TRASH || !array_key_exists(self::LABEL_TRASH, $note_labels)) {
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
