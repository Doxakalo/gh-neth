<?php

namespace console\controllers;

use Yii;
use common\models\User;
use common\models\Rbac;
use yii\console\Controller;
use yii\helpers\Console;


class RbacController extends Controller
{
	
	/**
	 * Create user
	 * 
	 * Run ./yii rbac/create-user email@example.com X123456 'John' 'Doe'
	 * 
	 * @param string $email
	 * @param string $password
	 * @param string $firstName
	 * @param string $lastName
	 */
    public function actionCreateUser($email, $password, $firstName, $lastName)
    {
		$user = new User();
		$user->email = $email;
		$user->password = $password;
		$user->first_name = $firstName;
		$user->last_name = $lastName;
		$user->nickname = $firstName . '-' . $lastName . '-' . substr(md5(uniqid()), 0, 3);
		$user->status = User::STATUS_ACTIVE;
		$user->generateAuthKey();
		
		try {
			$result = $user->validate() && $user->save();
			if($result) {
				$this->stdout(sprintf('User created: %s', $user->email), Console::FG_GREEN);
				$this->stdout("\n");
			} else {
				$this->stderr(var_export($user->errors), Console::FG_RED);
				$this->stdout("\n");
			}	
		} catch (\Exception $e) {
			$this->stderr($e->getMessage());
		}
    }
	

	/**
	 * Generate password has from plain input
	 * 
	 * Run ./yii rbac/generate-password 'abc123'
	 * 
	 */
    public function actionGeneratePassword($password)
    {
		$this->stdout("Generating password");	
		$this->stdout("\n");	
		$this->stdout("\n");	
	
		$hash = Yii::$app->getSecurity()->generatePasswordHash($password);
		$this->stdout($hash);
		$this->stdout("\n");	
		
		$this->stdout("\n");	
		$this->stdout("Done", Console::FG_GREEN);
		$this->stdout("\n");	
    }	
	

	/**
	 * Assign role to user
	 * 
	 * Run ./yii rbac/add-role-to-user 'role_user'|'role_admin' 'user@example.com'
	 * 
	 * @param string $role
	 * @param string $email
	 */	
    public function actionAddRoleToUser($role, $email) {
		$auth = Yii::$app->authManager;
		$authRole = $auth->getRole($role);
		$user = User::findByEmail($email);
		if ($authRole && $user) {
			$result = $auth->assign($authRole, $user->getId());

			if($result) {
				$this->stdout(sprintf('Assigned role \'%s\' to user \'%s\'.', $authRole->name, $user->email) . "\n", Console::FG_GREEN);
			} else {
				$this->stderr(sprintf('Failed to assign role \'%s\' to user \'%s\'.', $authRole->name, $user->email) . "\n", Console::FG_RED);
			}

			// Change user type
			switch($role) {
				case Rbac::ROLE_ADMIN:
					$user->type = User::USER_TYPE_ADMIN;
					break;
				case Rbac::ROLE_USER:
					$user->type = User::USER_TYPE_PUBLIC;
					break;
			}
			if($user->save()) {
				$this->stdout(sprintf('Changed user type to \'%s\'.', $user->type) . "\n", Console::FG_GREEN);
			}

		} else {
			$this->stderr('User or role not found.' . "\n");
		}
	}


	/**
	 * Init RBAC
	 * 
	 * Run ./yii rbac/init
	 * 
	 */	
	public function actionInit() {
		$auth = Yii::$app->authManager;
		// remove existing permissions
		$auth->removeAll();

		
		/**
		 * Define permissions
		 */
		$permUserView = $auth->createPermission(Rbac::PERM_USER_VIEW, 'View user');
		$permUserCreate = $auth->createPermission(Rbac::PERM_USER_CREATE, 'Create user');
		$permUserUpdate = $auth->createPermission(Rbac::PERM_USER_UPDATE, 'Update user');
		$permUserDelete = $auth->createPermission(Rbac::PERM_USER_DELETE, 'Delete user');
		$auth->add($permUserView);
		$auth->add($permUserCreate);
		$auth->add($permUserUpdate);		
		$auth->add($permUserDelete);	
		
		$permMatchView = $auth->createPermission(Rbac::PERM_MATCH_VIEW, 'View match');
		$permMatchUpdate = $auth->createPermission(Rbac::PERM_MATCH_UPDATE, 'Update match');
		$auth->add($permMatchView);
		$auth->add($permMatchUpdate);		

		$permAdminInterface = $auth->createPermission(Rbac::PERM_ADMIN_INTERFACE, 'Access admin interface');
		$auth->add($permAdminInterface);

		
		/**
		 * User role
		 */
		$commonUser = $auth->createRole(Rbac::ROLE_USER);
		$commonUser->description = 'User';
		$auth->add($commonUser);
		
		
		/**
		 * Admin role
		 */
		$admin = $auth->createRole(Rbac::ROLE_ADMIN);
		$admin->description = 'Admin';
		$auth->add($admin);

		$auth->addChild($admin, $permUserView); 
		$auth->addChild($admin, $permUserCreate); 
		$auth->addChild($admin, $permUserUpdate); 
		$auth->addChild($admin, $permUserDelete); 

		$auth->addChild($admin, $permMatchView);
		$auth->addChild($admin, $permMatchUpdate);

	}
	
}
