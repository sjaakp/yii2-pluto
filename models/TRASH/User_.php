<?php
namespace sjaakp\pluto\models;

use Yii;
use yii\base\InvalidCallException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\validators\InlineValidator;

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
 * @property string $password write-only password
 *
 * @method touch($attribute)
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 1;
    const STATUS_PENDING = 2;
    const STATUS_ACTIVE = 3;

    public $pw_check;

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
        $mod = Yii::$app->controller->module;

        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'unique', 'targetClass' => '\sjaakp\pluto\models\User', 'message' => 'This name has already been taken.'],
            ['name', 'string', 'min' => 2, 'max' => 60],

            ['email', 'trim', 'except' => 'delete'],
            ['email', 'required', 'except' => 'delete'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            ['email', 'unique', 'targetClass' => '\sjaakp\pluto\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required', 'on' => 'create'],
            ['password', 'match', 'pattern' => $mod->passwordRegexp, 'on' => ['create', 'update']],

            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_PENDING, self::STATUS_BLOCKED, self::STATUS_DELETED]],
            ['status', 'required', 'on' => ['create', 'update']],

            ['pw_check', 'required', 'on' => 'settings'],
            ['pw_check', 'string', 'min' => Yii::$app->params['user.minPasswordLength'] ?? 6, 'on' => 'settings'],
            ['pw_check', function($attr, $params, $validator) {
                /* @var $validator InlineValidator */
                if (! $this->validatePassword($this->$attr)) $validator->addError($this, $attr, 'Password incorrect.');
            }, 'on' => 'settings'],

            ['login_count', 'integer'],

            [[/*'singleRole',*/ 'roles', 'created_at', 'updated_at', 'blocked_at', 'lastlogin_at', 'login_count'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pw_check' => 'Your current password',
            'statusText' => 'Status',
            'singleRole' => 'Role',
            'lastlogin_at' => 'Last Login'
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
     * @param string $name
     * @return ActiveRecord | null
     */
    public static function findByUsernameOrEmail($name)
    {
        $r = static::findOne([ 'email' => $name, 'status' => self::STATUS_ACTIVE ]);
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
        $expire = Yii::$app->controller->module->tokenStamina;
        if ($timestamp + $expire < time()) return null;

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
        $this->token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     */
    public function removeToken()
    {
        $this->token = null;
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
     * Always return empty string
     *
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * If not empty, generates password hash and sets it to the model
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        if (! empty($password)) $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates authentication key
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

        if ($r) $this->afterDelete();
        return $r ? 1 : false;   // the number of Users effected | false
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        /* @var $profile yii\db\BaseActiveRecord */
        $profile = Yii::$app->profile ?? false;
        if ($profile)   {
            $pr = $profile::findOne($this->id);
            if ($pr) $pr->delete();
        }
        return parent::beforeDelete();
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        $stats = [
            self::STATUS_DELETED => 'deleted',
            self::STATUS_BLOCKED => 'blocked',
            self::STATUS_PENDING => 'pending',
            self::STATUS_ACTIVE => 'active',
        ];
        return $stats[$this->status];
    }

    /**
     * @return array of Role name => Role name
     * Exclude default roles
     */
    public function getRoles()
    {
        /* @var $auth yii\rbac\BaseManager */
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($this->id);
        $defaultRoles = $auth->getDefaultRoles();
        foreach($defaultRoles as $defaultRole)   {
            unset($roles[$defaultRole]);
        }
        return ArrayHelper::map($roles, 'name', 'name');
    }

    /**
     * @param $roleNames array of Role Name
     * @throws \Exception
     */
    public function setRoles($roleNames)
    {
        if ($this->isNewRecord) {
            throw new InvalidCallException('Can\'t call ' . __METHOD__ . ' if record is new.');
        }
        $auth = Yii::$app->authManager;
        $auth->revokeAll($this->id);
        foreach ($roleNames as $roleName)   {
            $role = $auth->getRole($roleName);
            $auth->assign($role, $this->id);
        }
    }

    /**
     * @return string|null Role name
     */
    public function getSingleRole()
    {
        $r = $this->getRoles();
        if (empty($r)) return null;
        if (count($r) > 1) return '<multiple>';
        return current($r);
    }

    /**
     * @param $roleName string
     * @throws \Exception
     */
    public function setSingleRole($roleName)
    {
        $this->setRoles([$roleName]);
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
