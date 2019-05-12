<?php
namespace sjaakp\pluto\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class PlutoController extends Controller
{
    /**
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $rules = [
            'Creator',
            'Updater',
            'CreatorOrUpdater',
            'Role',
            'NotRole'
        ];

        foreach ($rules as $rule)   {
            $className = "\sjaakp\pluto\\rbac\\{$rule}Rule";
            $r = new $className();
            $auth->add($r);

        }

        echo "Rules set\n";

        $permissions = [
            'createItem' => 'Create item',
            'deleteItem' => 'Delete item',
            'manageRoles' => 'Manage Roles and Permissions',
            'manageUsers' => 'Manage Users',
            'updateCommonUser' => 'Update user data, but not those of \'admin\'',
            'updateCreatedItem' => 'Update own item',
            'updateItem' => 'Update item',
            'updateUser' => 'Update user data',
        ];

        foreach($permissions as $key => $desc)  {
            $item = $auth->createPermission($key);
            $item->description = $desc;
            $permissions[$key] = $item;
            $auth->add($item);
        }

        $permissions['updateCommonUser']->ruleName = 'hasNotRole';
        $auth->addChild($permissions['updateCommonUser'], $permissions['updateUser']);

        $permissions['updateCreatedItem']->ruleName = 'isCreator';
        $auth->addChild($permissions['updateCreatedItem'], $permissions['updateItem']);

        echo "Permissions set\n";

        $support = $auth->createRole('support');
        $support->description = 'Can manage user data, but not those of \'admin\'';
        $auth->add($support);
        $auth->addChild($support, $permissions['manageUsers']);
        $auth->addChild($support, $permissions['updateCommonUser']);

        $admin = $auth->createRole('admin');
        $admin->description = 'Can do anything';
        $auth->add($admin);
        $auth->addChild($admin, $permissions['manageRoles']);
        $auth->addChild($admin, $permissions['updateUser']);
        $auth->addChild($admin, $support);

        echo "Roles set\nPluto completed\n";

        return ExitCode::OK;
    }
}
