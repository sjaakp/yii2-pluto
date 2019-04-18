<?php
namespace sjaakp\pluto\models;

use Yii;
use yii\base\InvalidCallException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use sjaakp\pluto\Module;

/**
 * User model
 *
 * @property integer $id
 * @property string $name
 * @property string $password_hash
 * @property string $email
 * @property string $auth_key
 * @property string $status
 * @property string $token
 * @property string $created_at
 * @property string $updated_at
 * @property string $blocked_at
 * @property string $deleted_at
 * @property string $lastlogin_at
 * @property integer $login_count
 *
 * @method touch($attribute)
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 1;
    const STATUS_PENDING = 2;
    const STATUS_ACTIVE = 3;

    public $password;
    public $password_repeat;
    public $captcha;
    public $roles = [];
    public $singleRole;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $mod = Module::getInstance();

        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'unique', 'targetClass' => '\sjaakp\pluto\models\User', 'message' => Yii::t('pluto', 'This name has already been taken.')],
            ['name', 'string', 'min' => 2, 'max' => 60],

            ['email', 'trim'],
            ['email', 'required', 'except' => 'delete'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            ['email', 'unique', 'targetClass' => '\sjaakp\pluto\models\User', 'message' => Yii::t('pluto', 'This email address has already been taken.')],

            ['password', 'required', 'on' => ['create', 'signup', 'recover', 'pw-change']],
            ['password', 'match', 'pattern' => $mod->passwordRegexp, 'on' => ['create', 'update', 'signup', 'recover', 'pw-change']],
            ['password', function($attribute, $params, $validator) {
                $this->encryptPassword($this->$attribute);
            }, 'on' => ['create', 'update', 'signup', 'recover', 'pw-change']],
            ['password', function($attribute, $params, $validator) {
                if (! $this->validatePassword($this->$attribute))   {
                    $this->addError($attribute, Yii::t('pluto', Yii::t('pluto', 'Incorrect password')));
                }
            }, 'on' => ['settings', 'download', 'delete']],

            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_PENDING, self::STATUS_BLOCKED, self::STATUS_DELETED]],
            ['status', 'required', 'on' => ['create', 'update']],

            ['password_repeat', 'required'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],

            ['captcha', 'required'],
            ['captcha', 'captcha', 'captchaAction' => Yii::$app->controller->module->id . '/default/captcha'],

            [['singleRole', 'roles'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('pluto', 'Username'),
            'email' => Yii::t('pluto', 'Email'),
            'password' => Yii::t('pluto', 'Password'),
            'password_repeat' => Yii::t('pluto', 'Password (again)'),
            'captcha' => Yii::t('pluto', 'I am not a robot'),
            'statusText' => Yii::t('pluto', 'Status'),
            'singleRole' => Yii::t('pluto', 'Role'),
            'created_at' => Yii::t('pluto', 'Created at'),
            'updated_at' => Yii::t('pluto', 'Updated at'),
            'blocked_at' => Yii::t('pluto', 'Blocked at'),
            'lastlogin_at' => Yii::t('pluto', 'Last Login at'),
            'login_count' => Yii::t('pluto', 'Login Count')
        ];
    }

    /**
     * {@inheritdoc}
     * IdentityInterface
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     * IdentityInterface
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Method ' . __CLASS__ . '::' . __METHOD__ . ' is not implemented.');
    }


    /**
     * {@inheritdoc}
     * IdentityInterface
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     * IdentityInterface
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     * IdentityInterface
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @param $email
     * @param int $status
     * @return User|null
     */
    public static function findByEmail($email, $status = self::STATUS_ACTIVE)
    {
        return static::findOne([ 'email' => $email, 'status' => $status ]);
    }

    /**
     * @param string $name
     * @return User | null
     */
    public static function findByUsernameOrEmail($name)
    {
        $r = static::findByEmail($name, self::STATUS_ACTIVE);
        if (! $r) $r = static::findOne([ 'name' => $name, 'status' => self::STATUS_ACTIVE ]);
        return $r;
    }

    /**
     * Finds user by token
     *
     * @param string $action
     * @param string $token
     * @param string $status
     * @return static|null
     */
    public static function findByToken($token, $status = self::STATUS_ACTIVE)
    {
        if (empty($token)) return null;

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        if ($timestamp < time()) return null;   // token expired

        return static::findOne([
            'token' => $token,
            'status' => $status,
        ]);
    }

    /**
     * Generates new token
     * @throws \yii\base\Exception
     */
    public function generateToken()
    {
        $expired = time() + Yii::$app->controller->module->tokenStamina;
        $this->token = Yii::$app->security->generateRandomString() . '_' . $expired;
    }

    /**
     */
    public function removeToken()
    {
        $this->token = null;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash and authentication key; sets them to the model
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function encryptPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @param $subject
     * @param $view
     * @param array $options
     * @return bool
     */
    public function sendEmail($subject, $view, $options = [])
    {
        $mailView = [
            'html' => "$view-html",
            'text' => "$view-text",
        ];
        $from = Yii::$app->params['supportEmail'] ?? Yii::$app->params['adminEmail'];
        $options['user'] = $this;

        $mailer = Yii::$app->mailer;
        foreach (Yii::$app->controller->module->mailOptions as $key => $value)  {
            $mailer->$key = $value;
        }
        return $mailer
            ->compose($mailView, $options)
            ->setFrom([$from => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject($subject)
            ->send();
    }

    /**
     * @param $subject
     * @param $view
     * @param array $options
     * @param $linkAction string - the action part in the link; if null, $view is taken
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendTokenEmail($subject, $view, $options = [], $linkAction = null)
    {
        if (is_null($this->token))
        {
            throw new InvalidCallException('User token is not set.');
        }
        if (is_null($linkAction)) $linkAction = $view;
        $module = Yii::$app->controller->module;

        $options['link'] = Yii::$app->urlManager->createAbsoluteUrl([$module->id . '/default/' . $linkAction, 'token' => $this->token]);

        return $this->sendEmail($subject, $view, $options);
    }

    /**
     * User is never removed from database.
     * In stead the status is set to STATUS_DELETED, name is changed into a generic name, and other attributes are cleared.
     * Thus dangling foreign pointers (i.e. created_by) are avoided.
     * Profile record, if it exists, is deleted completely.
     * @return false|int
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function delete()
    {
        if (!$this->beforeDelete()) {
            return false;
        }
        $this->scenario = 'delete';
        $this->status = self::STATUS_DELETED;
        $this->name = 'nn-' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT); // 'nn-' plus six digits
        $this->auth_key = null;
        $this->password_hash = null;
        $this->email = null;
        $this->deleted_at = new Expression('NOW()');
        $r = $this->save();

        $auth = Yii::$app->authManager;
        $auth->revokeAll($this->id);    // revoke all roles

        /* @var $profile yii\db\BaseActiveRecord */
        $profile = Yii::$app->profile ?? false;    // delete profile, if any
        if ($profile)   {
            $pr = $profile::findOne($this->id);
            if ($pr) $pr->delete();
        }

        if ($r) $this->afterDelete();
        return $r ? 1 : false;   // the number of Users effected | false
    }

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->prepareRoles();
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($insert) $this->generateAuthKey();
        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (! Module::getInstance()->multipleRoles && ! empty($this->singleRole)) {
            $this->roles = [ $this->singleRole ];
        }

        $auth = Yii::$app->authManager;
        $auth->revokeAll($this->id);    // revoke old roles
        foreach ($this->roles as $roleName)   {
            $role = $auth->getRole($roleName);
            $auth->assign($role, $this->id);    // assign new roles
        }

        if ($insert)    {
            $this->createProfile();
        }
    }

    /**
     *
     */
    public function afterFind()
    {
        parent::afterFind();

        /* @var $auth yii\rbac\BaseManager */
        $auth = Yii::$app->authManager;
        $userRoles = $auth->getRolesByUser($this->id);  // yii\rbacRole[]
        $defaultRoles = $auth->getDefaultRoles();   // string[]
        $this->roles = array_diff(array_keys($userRoles), $defaultRoles);

        $this->prepareRoles();
    }

    /**
     *
     */
    protected function prepareRoles()
    {
        if (! Module::getInstance()->multipleRoles && count($this->roles) == 1) {
            $this->singleRole =  current($this->roles);
        }
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        $stats = [
            self::STATUS_DELETED => Yii::t('pluto', 'deleted'),
            self::STATUS_BLOCKED => Yii::t('pluto', 'blocked'),
            self::STATUS_PENDING => Yii::t('pluto', 'pending'),
            self::STATUS_ACTIVE => Yii::t('pluto', 'active'),
        ];
        return $stats[$this->status];
    }

    /**
     * @return bool
     */
    public function createProfile()
    {
        $profile = Yii::$app->profile ?? false;
        if ($profile)  {
            $pk = current($profile->primaryKey());       // note: name of the primary key, not the value
            $profile->{$pk} = $this->id;
            if ($profile->hasAttribute('name')) {
                $profile->name = $this->name;
            }
            return $profile->save(false);
        }
        return true;
    }

    /**
     * @return \yii\db\BaseActiveRecord|null
     */
    public function getProfile()
    {
        /* @var $profile yii\db\BaseActiveRecord */
        $profile = Yii::$app->profile ?? false;
        if (! $profile) return null;
        return $profile::findOne($this->id);
    }
}
