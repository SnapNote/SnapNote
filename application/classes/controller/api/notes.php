<?php

class Controller_Api_Notes extends Controller_Api_Secure
{
	public function action_get()
	{
		$id = $this->request->param('id');
		$subset = $this->request->param('subset');
		$id2 = $this->request->param('id2');

		if(!empty($id)) {
			$note = ORM::factory('note', $id);
			if(!$note->loaded())
				throw new HTTP_Exception_404();
			if(!$note->can_view($this->user->id))
				throw new HTTP_Exception_403();

			if(!empty($subset)) {
				if($subset == 'labels' && empty($id2)) {
					$this->response($note->noteLabels($this->user->id));
				} else {
					throw new HTTP_Exception_404();
				}
			} else {
				$this->response($this->_output_note($note));
			}
		} else {
			$q = $this->request->query('q');
			if(!empty($q))
				$this->response(array('notes'=>$this->user->notes($q)));
			else
				$this->response(array('notes'=>$this->user->notes()));
		}
	}

	/**
	 * Creates a note.
     *
     * @param mixed $subject
     * @param mixed $note
	 * @throws HTTP_Exception_400
     */
	public function action_create()
	{
		$id = $this->request->param('id');
		$subset = $this->request->param('subset');
		$id2 = $this->request->param('id2');

		$request = $this->request->body();
		if(!empty($id)) {
			$note = ORM::factory('note', $id);
			if(!$note->loaded())
				throw new HTTP_Exception_404();
			if(!$note->can_view($this->user->id))
				throw new HTTP_Exception_403();
			if(!empty($subset)) {
				if($subset == 'labels' && empty($id2)) {
					$note->addLabel($request['label_id'], $this->user->id);
					$this->response($note->noteLabels($this->user->id));
				} else {
					throw new HTTP_Exception_404();
				}
			} else {
				throw new HTTP_Exception_404();
			}
		} else {
			$note = ORM::factory('note');
			$note->values($request,array('subject'));
			try {
				$note->save();
				$version = ORM::factory('note_version');
				$version->note_id = $note->id;
				$version->user_id = $this->user->id;
				$version->values($request,array('subject','note'));
				$version->status = 'active';
				$version->save();
				$note->active_note_version_id = $version->id;
				$note->save();
				$note_label = ORM::factory('note_label');
				$note_label->note_id = $note->id;
				$note_label->label_id = 1;
				$note_label->user_id = $this->user->id;
				$note_label->save();
				$note_user = ORM::factory('note_user');
				$note_user->note_id = $note->id;
				$note_user->user_id = $this->user->id;
				$note_user->permission = 'owner';
				$note_user->save();
				$this->response($this->_output_note($note));
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors('models');
				throw new HTTP_Exception_400(implode(', ', $errors));
			} catch (Exception $e) {
				$error = $e->getMessage();
				throw new HTTP_Exception_400($error);
			}
		}
	}

	/**
	 * Updates a note.
     *
     * @param mixed $id
     * @param mixed $subject
     * @param mixed $note
	 * @throws HTTP_Exception_404
	 * @throws HTTP_Exception_400
     */
	public function action_update()
	{
		$id = $this->request->param('id');
		$subset = $this->request->param('subset');
		$id2 = $this->request->param('id2');

		if(!empty($subset))
			throw new HTTP_Exception_500();
		if(empty($id))
			throw new HTTP_Exception_404();

		$note = ORM::factory('note', $id);
		if(!$note->loaded())
			throw new HTTP_Exception_404();

		$request = $this->request->body();
		if(in_array($note->permission($this->user->id), array('owner','can-edit'))) {
			$note->values($request,array('subject'));
			try {
				$note->deactivateVersions();
				$version = ORM::factory('note_version');
				$version->note_id = $note->id;
				$version->user_id = $this->user->id;
				$version->values($request,array('subject','note'));
				$version->status = 'active';
				$version->save();
				$note->active_note_version_id = $version->id;
				$note->save();
				$this->response($this->_output_note($note));
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors('models');
				throw new HTTP_Exception_400(implode(', ', $errors));
			} catch (Exception $e) {
				$error = $e->getMessage();
				throw new HTTP_Exception_400($error);
			}
		} else {
			throw new HTTP_Exception_403("Operation not permitted.");
		}
	}

	/**
	 * Deletes a label.
     *
     * @param mixed $id
	 * @throws HTTP_Exception_404
	 * @throws HTTP_Exception_400
     */
	public function action_delete()
	{
		$id = $this->request->param('id');
		$subset = $this->request->param('subset');
		$id2 = $this->request->param('id2');

		if(empty($id))
			throw new HTTP_Exception_404();

		$note = ORM::factory('note', $id);
		if(!$note->loaded())
			throw new HTTP_Exception_404();
		if(!empty($subset)) {
			if($subset == 'labels' && !empty($id2)) {
				$note->removeLabel($id2, $this->user->id);
				$this->response($note->noteLabels($this->user->id));
			} else {
				throw new HTTP_Exception_404();
			}
		} else {
			if($note->permission($this->user->id) == 'owner') {
				try {
					foreach($note->note_users->find_all() as $note_user)
						$note_user->delete();
					foreach($note->labels->find_all() as $label)
						$label->delete();
					foreach($note->versions->find_all() as $version)
						$version->delete();
					$note->delete();
					return true;
				} catch (Exception $e) {
					$error = $e->getMessage();
					throw new HTTP_Exception_400($error);
				}
			} else {
				throw new HTTP_Exception_403("Deleting not permitted.");
			}
		}
	}
	
	protected function _output_note($note)
	{
		$active_version = $note->active_version();
		$active_version_user = $active_version->user;
		return array(
			'id' => $note->id,
			'subject' => $note->subject,
			'created' => $note->created,
			'permission' => $note->permission($this->user->id),
			'active_version' => array(
				'subject' => $active_version->subject,
				'note' => $active_version->note,
				'status' => $active_version->status,
				'created' => $active_version->created,
				'modified' => $active_version->modified,
				'user' => array(
					'id' => $active_version_user->id,
					'name' => $active_version_user->name,
					'username' => $active_version_user->username,
				),
			),
			'labels' => $note->noteLabels($this->user->id),
		);
	}
}
