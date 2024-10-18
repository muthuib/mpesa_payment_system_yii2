<?php

namespace app\controllers;

use Yii;
use yii\rbac\Item;
use yii\web\Controller;
use app\models\AssignRoleForm;
use app\models\AssignPermissionForm;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class RoleController extends Controller
{
    // Restrict access to authenticated users with admin privileges
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'], // Admin-only access
                    ],
                ],
            ],
        ];
    }

    // List all roles and permissions
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();
        $permissions = $auth->getPermissions();
        return $this->render('index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    // Create a new role
    public function actionCreateRole()
    {
        $auth = Yii::$app->authManager;
        $model = new \app\models\RoleForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $role = $auth->createRole($model->name);
            $role->description = $model->description;
            $auth->add($role);

            Yii::$app->session->setFlash('success', 'Role created successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('create-role', ['model' => $model]);
    }

    // Update an existing role
    public function actionUpdateRole($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);

        if (!$role) {
            throw new NotFoundHttpException("Role not found.");
        }

        $model = new \app\models\RoleForm(['name' => $role->name, 'description' => $role->description]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $role->name = $model->name;
            $role->description = $model->description;
            $auth->update($name, $role);

            Yii::$app->session->setFlash('success', 'Role updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('update-role', ['model' => $model]);
    }

    // Delete an existing role
    public function actionDeleteRole($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);

        if (!$role) {
            throw new NotFoundHttpException("Role not found.");
        }

        $auth->remove($role);
        Yii::$app->session->setFlash('success', 'Role deleted successfully.');
        return $this->redirect(['index']);
    }

    // Assign roles to users
    public function actionAssignRole($userId)
    {
        $auth = Yii::$app->authManager;
        $model = new AssignRoleForm();

        if ($model->load(Yii::$app->request->post())) {
            // Remove all existing roles for the user
            $auth->revokeAll($userId);

            // Assign new role
            $role = $auth->getRole($model->role);
            $auth->assign($role, $userId);

            Yii::$app->session->setFlash('success', 'Role assigned successfully.');
            return $this->redirect(['user/index']); // Redirect to users page after assigning
        }

        $availableRoles = $auth->getRoles();
        return $this->render('assign-role', [
            'model' => $model,
            'availableRoles' => $availableRoles,
            'userId' => $userId,
        ]);
    }
    // CODE TO ASSIGN OR REMOVE PERMISSION FROM A ROLE
    public function actionAssignPermission($roleName)
{
    $auth = Yii::$app->authManager;
    $role = $auth->getRole($roleName);

    if (!$role) {
        throw new NotFoundHttpException("Role not found.");
    }

    // Fetch available permissions
    $permissions = $auth->getPermissions();
    $rolePermissions = $auth->getPermissionsByRole($roleName);
    $assignedPermissions = array_keys($rolePermissions);

    $model = new \app\models\AssignPermissionForm(['permissions' => $assignedPermissions]);

    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        // Clear old permissions
        $auth->removeChildren($role);

        // Assign selected permissions
        foreach ($model->permissions as $permissionName) {
            $permission = $auth->getPermission($permissionName);
            if ($permission) {
                $auth->addChild($role, $permission);
            }
        }

        Yii::$app->session->setFlash('success', 'Permissions updated successfully.');
        return $this->redirect(['index']);
    } 

    return $this->render('assign-permission', [
        'role' => $role,
        'permissions' => $permissions,
        'model' => $model,
    ]);
}

}