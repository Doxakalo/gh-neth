<?php

namespace common\models;

class Rbac {
	
	/* Permissions */
	const PERM_USER_VIEW = 'user_view';
	const PERM_USER_CREATE = 'user_create';
	const PERM_USER_UPDATE = 'user_update';
	const PERM_USER_DELETE = 'user_delete';

	const PERM_MATCH_VIEW = 'match_view';
	const PERM_MATCH_UPDATE = 'match_update';
	
	const PERM_ADMIN_INTERFACE = 'admin_interface';

	/* Roles */
	const ROLE_USER = 'role_user';
	const ROLE_ADMIN = 'role_admin';
	
}
