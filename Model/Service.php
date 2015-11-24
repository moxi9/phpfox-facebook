<?php

namespace Apps\PHPfox_Facebook\Model;

/**
 * Service class for Facebook Connect App
 *
 * @package Apps\PHPfox_Facebook\Model
 */
class Service extends \Core\Model {

	/**
	 * Create a new user or log them in if they exist
	 *
	 * @param \Facebook\GraphUser $fb
	 * @return bool
	 * @throws \Exception
	 */
	public function create(\Facebook\GraphUser $fb) {
		$email = $fb->getId() . '@fb';
		$user = $this->db->select('*')->from(':user')->where(['email' => $email])->get();
		if (isset($user['user_id'])) {
			$_password = $fb->getId() . uniqid();
			$password = (new \Core\Hash())->make($_password);
			$this->db->update(':user', ['password' => $password], ['user_id' => $user['user_id']]);
		}
		else {
			$_password = $fb->getId() . uniqid();
			$password = (new \Core\Hash())->make($_password);
			$id = $this->db->insert(':user', [
				'user_group_id' => NORMAL_USER_ID,
				'email' => $email,
				'password' => $password,
				'full_name' => $fb->getName(),
				'user_name' => 'fb-' . $fb->getId(),
				'user_image' => '{"fb":"' . $fb->getId() . '"}',
				'joined' => PHPFOX_TIME,
				'last_activity' => PHPFOX_TIME
			]);

			$tables = [
				'user_activity',
				'user_field',
				'user_space',
				'user_count'
			];
			foreach ($tables as $table) {
				$this->db->insert(':' . $table, ['user_id' => $id]);
			}
		}

		\User_Service_Auth::instance()->login($email, $_password, true, 'email');
		if (!\Phpfox_Error::isPassed()) {
			throw new \Exception(implode('', \Phpfox_Error::get()));
		}

		return true;
	}
}
