<?php

namespace app\models;

use yii\base\Model;
use Yii;

class AssignPermissionForm extends Model
{
    public $permissions = [];

    public function rules()
    {
        return [
            ['permissions', 'safe'], // Allow an array of permissions
        ];
    }

    public function assignPermissions($roleName)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);

        if ($role === null) {
            return false; // Role not found
        }

        // Remove existing permissions from role
        $auth->removeChildren($role);

        // Add the selected permissions to the role
        foreach ($this->permissions as $permissionName) {
            $permission = $auth->getPermission($permissionName);
            if ($permission !== null) {
                $auth->addChild($role, $permission);
            }
        }

        return true;
    }
}