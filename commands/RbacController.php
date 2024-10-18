<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        
        // CREATE PERMISSIONS
        //  (check if the permission already exist)
        $createPost = $auth->getPermission('createPost');
        if ($createPost === null) {
        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);
        }

        //  (check if the permission already exist)
        $updatePost = $auth->getPermission('updatePost');
        if ($updatePost === null) {
        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update a post';
        $auth->add($updatePost);
        }

        //adding other permissions
        //  (check if the permission already exist)
        $viewPost = $auth->getPermission('viewPost');
        if ($viewPost === null) {
            $viewPost = $auth->createPermission('viewPost');
            $viewPost->description = 'view a post';
            $auth->add($viewPost);
        }

        //  (check if the permission already exist)
        $deletePost = $auth->getPermission('deletePost');
        if ($deletePost === null) {
            $deletePost = $auth->createPermission('deletePost');
            $deletePost->description = 'delete a post';
            $auth->add($deletePost);
        }

        // permission to hide unauthorised user/roles from seeing manage users in sidebar
        //  (check if the permission already exist)
        $manageUsers = $auth->getPermission('manageUsers');
        if ($manageUsers === null) {
            $manageUsers = $auth->createPermission('manageUsers');
            $manageUsers->description = 'manage Users';
            $auth->add($manageUsers);
        }        
        
        // CREATE ROLES AND ASSIGN ROLES
        //check if the role exists
        $admin = $auth->getRole('admin');
        if ($admin === null) {
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        }
        // Assign permissions only if they haven't been assigned yet
        if (!$auth->hasChild($admin, $createPost)) {
            $auth->addChild($admin, $createPost);
        }
        if (!$auth->hasChild($admin, $updatePost)) {
            $auth->addChild($admin, $updatePost);
        }
        if (!$auth->hasChild($admin, $deletePost)) {
            $auth->addChild($admin, $deletePost);
        }
        if (!$auth->hasChild($admin, $viewPost)) {
            $auth->addChild($admin, $viewPost);
        }
        //assign admin role a permission to manage users
        if (!$auth->hasChild($admin, $manageUsers)) {
            $auth->addChild($admin, $manageUsers);
        }


        //check if the role exists
        $editor = $auth->getRole('editor');
        if ($editor === null) {
        $editor = $auth->createRole('editor');
        $auth->add($editor);
        }
        if (!$auth->hasChild($editor, $createPost)) {
            $auth->addChild($editor, $createPost);
        }
        if (!$auth->hasChild($editor, $updatePost)) {
            $auth->addChild($editor, $updatePost);
        }
        if (!$auth->hasChild($editor, $deletePost)) {
            $auth->addChild($editor, $deletePost);
        }
        if (!$auth->hasChild($editor, $viewPost)) {
            $auth->addChild($editor, $viewPost);
        }

        //check if the role exists
        $viewer = $auth->getRole('viewer');
        if ($viewer === null) { 
        $viewer = $auth->createRole('viewer');
        $auth->add($viewer);
        }
        // Assign permissions based on your desired permissions for the viewer role
        if (!$auth->hasChild($viewer, $createPost)) {
            $auth->addChild($viewer, $createPost);
        }
        if (!$auth->hasChild($viewer, $updatePost)) {
            $auth->addChild($viewer, $updatePost);
        }
        if (!$auth->hasChild($viewer, $deletePost)) {
            $auth->addChild($viewer, $deletePost);
        }
        if (!$auth->hasChild($viewer, $viewPost)) {
            $auth->addChild($viewer, $viewPost);
        }

        echo "RBAC roles and permissions have been initialized.\n";
    }
    public function actionAssign()
    {
        $auth = Yii::$app->authManager;

        // Assign role to user
        $auth->assign($auth->getRole('admin'), 1); // Assign admin role to user with ID 1
        $auth->assign($auth->getRole('editor'), 2); // Assign editor role to user with ID 2
        $auth->assign($auth->getRole('viewer'), 3); // Assign editor role to user with ID 3


        echo "Roles have been assigned to users.\n";
    }

}