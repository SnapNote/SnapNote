<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Userbootstrap_Controller_User {
	public $user_model_fields = array(
		'username', 
		'password', 
		'email',
		'name',
		'api_key'
	);

	public function action_register()
	{
		if(!Kohana::$config->load('useradmin.register_enabled'))
			$this->request->redirect('user/login');
		// Load reCaptcha if needed
		if (Kohana::$config->load('useradmin')->captcha)
		{
			include Kohana::find_file('vendor', 'recaptcha/recaptchalib');
			$recaptcha_config = Kohana::$config->load('recaptcha');
			$recaptcha_error = null;
		}
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('User registration');
		// If user already signed-in
		if (Auth::instance()->logged_in() != false)
		{
			// redirect to the user account
			$this->request->redirect('user/profile');
		}
		// Load the view
		$view = View::factory('user/register');
		// If there is a post and $_POST is not empty
		if ($_POST)
		{
			// optional checks (e.g. reCaptcha or some other additional check)
			$optional_checks = true;
			// if configured to use captcha, check the reCaptcha result
			if (Kohana::$config->load('useradmin')->captcha)
			{
				$recaptcha_resp = recaptcha_check_answer(
					$recaptcha_config['privatekey'], 
					$_SERVER['REMOTE_ADDR'], 
					$_POST['recaptcha_challenge_field'], 
					$_POST['recaptcha_response_field']
				);
				if (! $recaptcha_resp->is_valid)
				{
					$optional_checks = false;
					$recaptcha_error = $recaptcha_resp->error;
					Message::add('error', __('The captcha text is incorrect, please try again.'));
				}
			}
			try
			{
				if (! $optional_checks)
				{
					throw new ORM_Validation_Exception("Invalid option checks", new Validation(array()));
				}
				//$user = ORM::factory('user');
				if(isset($_POST['provider_id'])) {
					$_POST['password'] = $user->generate_password(42);
					$_POST['password_confirm'] = $_POST['password'];
				}
				if($_POST['username'] == '')
					$_POST['username'] = $user->generate_username($_POST['email']);
       			$_POST['api_key'] = md5($_POST['email'].time().rand(1,10000));
				Auth::instance()->register($_POST, TRUE);
				// sign the user in
				Auth::instance()->login($_POST['username'], $_POST['password']);
				// redirect to the user account
				$user = new Model_User(Auth::instance()->get_user()->id);
				$user->name = $_POST['name'];
				$user->api_key = $_POST['api_key'];
				$user->save();
				if(isset($_POST['provider_name']) && isset($_POST['provider_id']))
				{
					$user_identity = ORM::factory('user_identity');
					$user_identity->user_id  = $user->id;
					$user_identity->provider = $_POST['provider_name'];
					$user_identity->identity = $_POST['provider_id'];
					$user_identity->save();
				}
				$this->request->redirect('user/profile?first=true');
			}
			catch (ORM_Validation_Exception $e)
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				$errors = $e->errors('register');
				// Move external errors to main array, for post helper compatibility
				$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
				$view->set('errors', $errors);
				// Pass on the old form values
				$_POST['password'] = $_POST['password_confirm'] = '';
				$view->set('defaults', $_POST);
			}
		} else {
			$view->set('no_post', true);
		}
		if(isset($_POST['provider_name']))
			$view->set('provider_name', $_POST['provider_name']);
		if(isset($_POST['provider_id']))
			$view->set('provider_id', $_POST['provider_id']);
		if (Kohana::$config->load('useradmin')->captcha)
		{
			$view->set('captcha_enabled', true);
			$view->set('recaptcha_html', recaptcha_get_html($recaptcha_config['publickey'], $recaptcha_error));
		}
		$this->template->content = $view;
	}

	function action_regeneratekey()
	{
		$user = new Model_User(Auth::instance()->get_user()->id);
		$user->api_key = md5($user->email.time().rand(1,10000));
		$user->save();
		$this->request->redirect('user/profile');
	}

	function action_provider_return()
	{
		$provider_name = $this->request->param('provider');
		$provider = Provider::factory($provider_name);
		if (! is_object($provider))
		{
			Message::add('error', 'Provider is not enabled; please select another provider or log in normally.');
			$this->request->redirect('user/login');
			return;
		}
		// verify the request
		if ($provider->verify())
		{
			// check for previously connected user
			$uid = $provider->user_id();
			$user_identity = ORM::factory('user_identity')
				->where('provider', '=', $provider_name)
				->and_where('identity', '=', $uid)
				->find();
			if ($user_identity->loaded())
			{
				$user = $user_identity->user;
				if ($user->loaded() && $user->id == $user_identity->user_id && is_numeric($user->id))
				{
					// found, log user in
					Auth::instance()->force_login($user);
					// redirect to the user account
					$this->request->redirect(Cookie::get('returnUrl', Session::instance()->get_once('returnUrl','user/profile')));
					return;
				}
			}
			// If register is disabled, don't create new account
			if(!Kohana::$config->load('useradmin.register_enabled'))
				$this->request->redirect('user/login');
			// create new account
			if (! Auth::instance()->logged_in())
			{
				// Instantiate a new user
				$user = ORM::factory('user');
				// fill in values
				// generate long random password (maximum that passes validation is 42 characters)
				$password = $user->generate_password(42);
				$values = array(
					'name' => $provider->name(),
					// get a unused username like firstname.surname or firstname.surname2 ...
					'username' => $user->generate_username(
						str_replace(' ', '.', $provider->name())
					), 
					'password' => $password, 
					'password_confirm' => $password
				);
				if (Valid::email($provider->email(), TRUE))
				{
					$values['email'] = $provider->email();
				}
				try
				{
					// If the post data validates using the rules setup in the user model
					$user->create_user($values, $this->user_model_fields);
					// Add the login role to the user (add a row to the db)
					$login_role = new Model_Role(array(
						'name' => 'login'
					));
					$user->add('roles', $login_role);
					// create user identity after we have the user id
					$user_identity = ORM::factory('user_identity');
					$user_identity->user_id  = $user->id;
					$user_identity->provider = $provider_name;
					$user_identity->identity = $provider->user_id();
					$user_identity->save();
					// sign the user in
					Auth::instance()->login($values['username'], $password);
					// redirect to the user account
					$this->request->redirect('user/profile?first=true');
				}
				catch (ORM_Validation_Exception $e)
				{
					if ($provider_name == 'twitter')
					{
						Message::add('error', 'The Twitter API does not support retrieving your email address; you will have to enter it manually.');
					}
					else
					{
						Message::add('error', 'We have successfully retrieved some of the data from your other account, but we were unable to get all the required fields. Please complete form below to register an account.');
					}
					// in case the data for some reason fails, the user will still see something sensible:
					// the normal registration form.
					$view = View::factory('user/register');
					$errors = $e->errors('register');
					// Move external errors to main array, for post helper compatibility
					$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
					if(!isset($values['email'])) {
						unset($errors['email']);
						unset($values['email']);
					}
					$view->set('errors', $errors);
					// Pass on the old form values
					$values['password'] = $values['password_confirm'] = '';
					$view->set('defaults', $values);
					$view->set('provider_name', $provider_name);
					$view->set('provider_id', $provider->user_id());
					$view->set('provider_screen', true);
					if (Kohana::$config->load('useradmin')->captcha)
					{
						// FIXME: Is this the best place to include and use recaptcha?
						include Kohana::find_file('vendor', 'recaptcha/recaptchalib');
						$recaptcha_config = Kohana::$config->load('recaptcha');
						$recaptcha_error = null;
						$view->set('captcha_enabled', true);
						$view->set('recaptcha_html', recaptcha_get_html($recaptcha_config['publickey'], $recaptcha_error));
					}
					$this->template->content = $view;
				}
			}
			else
			{
				Message::add('error', 'You are logged in, but the email received from the provider does not match the email associated with your account.');
				$this->request->redirect('user/profile');
			}
		}
		else
		{
			Message::add('error', 'Retrieving information from the provider failed. Please register below.');
			$this->request->redirect('user/register');
		}
	}
}