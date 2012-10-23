<?php

class Controller_Api_Labels extends Controller_Api_Secure
{
	/**
	 * Retrieves the labels for a specific user,
	 * or a specific label and associated notes.
     *
     * @param mixed $id
     * @throws HTTP_Exception_404
     */
	public function action_get()
	{
		$id = $this->request->param('id');

		if(!empty($id)) {
			$label = ORM::factory('label', $id);
			if(!$label->loaded())
				throw new HTTP_Exception_404();
			$this->response(array(
				'id' => $label->id,
				'parent_id' => $label->parent_id,
				'name' => $label->name,
				'notes' => $label->notes($this->user->id),
			));
		} else {
			$master_labels = array();
			foreach(Model_Label::masterLabels($this->user->id) as $label) {
				$master_labels[$label['id']] = $label;
			}
			$user_labels = array();
			foreach(Model_Label::userLabels($this->user->id) as $label) {
				$user_labels[$label['id']] = $label;
			}
			$this->response(array('master'=>$master_labels, 'user'=>$user_labels));
		}
	}

	/**
	 * Creates a label.
     *
     * @param mixed $name
     * @param mixed $parent_id
	 * @param mixed $sort_order
	 * @throws HTTP_Exception_400
     */
	public function action_create()
	{
		$request = $this->request->body();
		$label = ORM::factory('label');
		$label->values($request,array('name','parent_id','sort_order'));
		$label->user_id = $this->user->id;
		try {
			$label->save();
			$this->response(array(
				'id' => $label->id,
				'name' => $label->name,
				'notes' => $label->notes($this->user->id),
			));
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors('models');
			throw new HTTP_Exception_400(implode(', ', $errors));
		} catch (Exception $e) {
			$error = $e->getMessage();
			throw new HTTP_Exception_400($error);
		}
	}

	/**
	 * Updates a label.
     *
     * @param mixed $id
     * @param mixed $name
     * @param mixed $parent_id
	 * @param mixed $sort_order
	 * @throws HTTP_Exception_404
	 * @throws HTTP_Exception_400
     */
	public function action_update()
	{
		$id = $this->request->param('id');

		if(empty($id))
			throw new HTTP_Exception_404();

		$label = ORM::factory('label', $id);
		if(!$label->loaded())
			throw new HTTP_Exception_404();

		$request = $this->request->body();
		$label->values($request,array('name','parent_id','sort_order'));
		$label->user_id = $this->user->id;
		try {
			$label->save();
			$this->response(array(
				'id' => $label->id,
				'name' => $label->name,
				'notes' => $label->notes($this->user->id),
			));
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors('models');
			throw new HTTP_Exception_400(implode(', ', $errors));
		} catch (Exception $e) {
			$error = $e->getMessage();
			throw new HTTP_Exception_400($error);
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

		if(empty($id))
			throw new HTTP_Exception_404();

		$label = ORM::factory('label', $id);
		if(!$label->loaded())
			throw new HTTP_Exception_404();
		if(count($label->children->find_all()) == 0) {
			try {
				$label->delete();
				return true;
			} catch (Exception $e) {
				$error = $e->getMessage();
				throw new HTTP_Exception_400($error);
			}
		} else {
			throw new HTTP_Exception_400("Children exist");
		}
	}
}
