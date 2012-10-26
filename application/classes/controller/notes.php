<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Notes extends Controller_App {
	public $auth_required = 'login';

    public function before()
    {
        parent::before();
		$this->template->title = 'SnapNote';
		$this->template->header = 'SnapNote';
	}

	public function action_index()
	{
		$this->request->redirect('/notes/label/1');
	}

	public function action_label()
	{
		$id = $this->request->param('id');
		
		$label = Dispatch::factory('api/labels/'.$id)->find();
		if(!$label->loaded())
			throw new Kohana_404_Exception();

		Session::instance()->set('label_id', $id);
		
		$this->template->header = $label['name'];
        $this->template->content = View::factory('notes/label')
			->set('label', $label)
			->set('notes', $label['notes']);
	}
	
	public function action_star()
	{
		$id = $this->request->param('id');

		$note = Dispatch::factory('api/notes/'.$id)->find();
		if(!$note->loaded())
			throw new Kohana_404_Exception();

		if(!empty($_REQUEST['action'])) {
			if($_REQUEST['action'] == 'unstar') {
				$noteLabel = Dispatch::factory('api/notes/'.$id.'/labels/'.Model_Label::LABEL_STARRED);
				$noteLabel->delete();
			} elseif($_REQUEST['action'] == 'star') {
				$noteLabel = Dispatch::factory('api/notes/'.$id.'/labels');
				$noteLabel->set('label_id', Model_Label::LABEL_STARRED);
				$noteLabel->create();
			}
		}
		if(!empty($_REQUEST['redirect']))
			$this->request->redirect($_REQUEST['redirect']);
	}

	public function action_search()
	{
		if(empty($_REQUEST['q']))
			$this->request->redirect('/');

		$notes = Dispatch::factory('api/notes?q='.$_REQUEST['q'])->find();
		if(!$notes->loaded())
			throw new Kohana_404_Exception();

		$this->template->header = 'Search: '.$_REQUEST['q'];
        $this->template->content = View::factory('notes/label')
			->set('notes', $notes['notes']);
	}

	public function action_archive()
	{
		$notes = Dispatch::factory('api/notes')->find();
		if(!$notes->loaded())
			throw new Kohana_404_Exception();

		$this->template->header = 'Archive';
        $this->template->content = View::factory('notes/label')
			->set('notes', $notes['notes']);
	}

	public function action_view()
	{
		$id = $this->request->param('id');

		$note = Dispatch::factory('api/notes/'.$id)->find();
		if(!$note->loaded())
			throw new Kohana_404_Exception();

		$this->template->header = $note['active_version']['subject'];
		$this->template->content = View::factory('notes/view')
			->set('note', $note);
	}

	public function action_edit()
	{
		$id = $this->request->param('id');

		if(!empty($id)) {
			$note = Dispatch::factory('api/notes/'.$id)->find();
			if(!$note->loaded())
				throw new Kohana_404_Exception();
		} else {
			$note = array();
		}

		$errors = null;
		if(!empty($_GET['remove_label'])) {
			$noteLabel = Dispatch::factory('api/notes/'.$id.'/labels/'.$_GET['remove_label']);
			$noteLabel->delete();
			$this->request->redirect('/notes/edit/'.$id);
		} else if($_POST)
		{
			if(!empty($_POST['action']) && $_POST['action'] == 'addLabel') {
				$noteLabel = Dispatch::factory('api/notes/'.$id.'/labels');
				$noteLabel->set('label_id', $_POST['label_id']);
				$noteLabel->create();
				$this->request->redirect('/notes/edit/'.$id);
			} else {
				if(!empty($id))
					$note = Dispatch::factory('api/notes/'.$id);
				else
					$note = Dispatch::factory('api/notes');
				$note->set('subject', $_POST['subject']);
				$note->set('note', $_POST['note']);
				try {
					if(!empty($id))
						$result = $note->update();
					else
						$result = $note->create();
					if($result->loaded())
						if(!empty($result['id']))
							$this->request->redirect('/notes/edit/'.$result['id']);
					$note = $result;
				} catch (ORM_Validation_Exception $e) {
					$errors = $e->errors('models');
				} catch (Exception $e) {
					$errors[] = $e->getMessage();
				}
			}
		}

		$master_labels = array();
		$user_labels = array();
		$labels = Dispatch::factory('api/labels')->find();
		if($labels->loaded()) {
			$master_labels = $labels['master'];
			$user_labels = $labels['user'];
		}
		
		$this->template->header = 'Edit Note';
		$this->template->content = View::factory('notes/edit')
			->set('master_labels', $master_labels)
			->set('user_labels', $user_labels)
			->set('note', $note);
	}

	public function action_delete()
	{
		$id = $this->request->param('id');
		$note = Dispatch::factory('api/notes/'.$id);
		$note->delete();
		$this->request->redirect('/');
	}
	
	public function action_labels()
	{
		$this->template->header = 'Manage Labels';
		if(!empty($_GET['id'])) {
			if(!empty($_GET['sort_order'])) {
				$label = Dispatch::factory('api/labels/'.$_GET['id']);
				$label->set('sort_order', $_GET['sort_order']);
				$result = $label->update();
			}
			$this->request->redirect('/notes/labels');
		}
		if(!empty($_GET['delete_id'])) {
			try {
				$label = Dispatch::factory('api/labels/'.$_GET['delete_id']);
				$result = $label->delete();
			} catch(Exception $e) {}
			$this->request->redirect('/notes/labels');
		}
		if($_POST)
		{
			if(!empty($_POST['action'])) {
				if($_POST['action'] == 'addLabel' && !empty($_POST['name'])) {
					$label = Dispatch::factory('api/labels');
					$label->set('name', $_POST['name']);
					if(!empty($_POST['parent_id']))
						$label->set('parent_id', $_POST['parent_id']);
					$result = $label->create();
					$this->request->redirect('/notes/labels');
				}
			}
		}
		$labels = Dispatch::factory('api/labels')->find();
		if($labels->loaded()) {
			$this->template->content = View::factory('notes/labels')
					->set('user_labels', $labels['user']);
		} else {
			$this->template->content = '';
		}
	}
}
