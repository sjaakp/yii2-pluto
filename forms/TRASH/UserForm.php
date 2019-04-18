<?php
namespace sjaakp\pluto\forms;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class UserForm extends Model
{
    public $name;
    public $email;
    public $password;
    public $status;
    public $created_at;
    public $updated_at;
    public $blocked_at;
    public $lastlogin_at;
    public $login_count;
    public $singleRole;
    public $roles;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $mod = Yii::$app->controller->module;
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'unique', 'targetClass' => '\sjaakp\pluto\models\User', 'message' => 'This name has already been taken.', 'on' => 'create'],
            ['name', 'string', 'min' => 2, 'max' => 60],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            ['email', 'unique', 'targetClass' => '\sjaakp\pluto\models\User', 'message' => 'This email address has already been taken.', 'on' => 'create'],

            ['password', 'required', 'on' => 'create'],
            ['password', 'match', 'pattern' => $mod->passwordRegexp],

            [['status', 'singleRole', 'roles'], 'safe'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'singleRole' => 'Role',
        ];
    }
}
