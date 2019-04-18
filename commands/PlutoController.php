<?php
namespace sjaakp\pluto\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class PlutoController extends Controller
{
    /**
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // add Rules
        $creatorRule = new \sjaakp\pluto\rbac\CreatorRule();
        $auth->add($creatorRule);
        $updaterRule = new \sjaakp\pluto\rbac\UpdaterRule();
        $auth->add($updaterRule);
        $creatorOrUpdaterRule = new \sjaakp\pluto\rbac\CreatorOrUpdaterRule();
        $auth->add($creatorOrUpdaterRule);

        echo "Rules set\n";

        // add Permissions
        $viewItem = $auth->createPermission('viewItem');
        $viewItem->description = 'View item';
        $auth->add($viewItem);

        $createItem = $auth->createPermission('createItem');
        $createItem->description = 'Create item';
        $auth->add($createItem);

        $updateItem = $auth->createPermission('updateItem');
        $updateItem->description = 'Update item';
        $auth->add($updateItem);

        $updateCreatedItem = $auth->createPermission('updateCreatedItem');
        $updateCreatedItem->description = 'Update user-created item';
        $updateCreatedItem->ruleName = $creatorRule->name;
        $auth->add($updateCreatedItem);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Manage Users';
        $auth->add($manageUsers);

        $manageRoles = $auth->createPermission('manageRoles');
        $manageRoles->description = 'Manage Roles and Permissions';
        $auth->add($manageRoles);
        
        

        // 'updateCreatedItem' will be used from 'updateItem'
        $auth->addChild($updateCreatedItem, $updateItem);

        echo "Permissions set\n";
        
        // add Roles and give Permissions
        // add 'visitor' role and give this role the 'viewItem' permission
        $visitor = $auth->createRole('visitor');
        $auth->add($visitor);
        $auth->addChild($visitor, $viewItem);

        // add 'author' role and give this role the 'createItem' permission
        // as well as the permissions of the 'visitor' role
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createItem);
        $auth->addChild($author, $visitor);

        // add 'editor' role and give this role the 'updateItem' permission
        // as well as the permissions of the 'author' role
        $editor = $auth->createRole('editor');
        $auth->add($editor);
        $auth->addChild($editor, $updateItem);
        $auth->addChild($editor, $author);

        // add 'support' role and give this role the 'manageUsers' permission
        // as well as the permissions of the 'visitor' role; 'support' doesn't need createItem or updateItem
        $support = $auth->createRole('support');
        $auth->add($support);
        $auth->addChild($support, $manageUsers);
        $auth->addChild($support, $visitor);

        // add 'admin' role and give this role the 'manageRoles' permission
        // as well as the permissions of the 'editor' and 'support' roles; 'admin' has all permissions
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manageRoles);
        $auth->addChild($admin, $editor);
        $auth->addChild($admin, $support);

        echo "Roles set\nPluto completed\n";

        return ExitCode::OK;
    }
}
