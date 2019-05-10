<?php

namespace sjaakp\pluto;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module as YiiModule;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;
use yii\web\UserEvent;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use sjaakp\pluto\models\User;

/**
 * user module definition class
 */
class Module extends YiiModule implements BootstrapInterface
{
    const PW_REVEAL = 0b0001;   // dialog has reveal password button
    const PW_DOUBLE = 0b0010;   // dialog has double password (user must fill in password twice (doesn't affect 'forgot', 'resend')
    const PW_CAPTCHA = 0b0100;  // dialog has captcha field

    /**
     * @var array options for certain aspects of views
     */
    public $viewOptions = [
        'row' => [ 'class' => 'row justify-content-center' ],
        'col' => [ 'class' => 'col-md-6 col-lg-5' ],
        'button' => [ 'class' => 'btn btn-success' ],
        'link' => [ 'class' => 'btn btn-sm btn-secondary' ],
    ];

    /**
     * @var array locations of view override files
     */
    public $views = [
//        'default' => [
//            'settings' => '@app/views/pluto/default/settings.php'
//        ]
    ];

    /**
     * @var array options for app mailer
     * @link https://www.yiiframework.com/doc/api/2.0/yii-mail-basemailer
     * If you want to override the mailer views, set viewPath.
     */
    public $mailOptions = [
        'viewPath' => '@sjaakp/pluto/mail',
        'htmlLayout' => '@sjaakp/pluto/mail/layouts/html',
        'textLayout' => '@sjaakp/pluto/mail/layouts/text',
    ];

    /**
     * @var array
     *  key: one of the actions ('signup', 'login', 'forgot', 'recover', 'resend', 'pw_change', 'delete'
     *  key may also be 'all', in which case the value applies to all actions
     *  value: any combination of above PW_xxx consts. Consts may be added or or'ed.
     */
    public $passwordFlags = [
        'all' => self::PW_REVEAL,
//        'delete' => self::PW_DOUBLE | self::PW_CAPTCHA,
    ];

    /**
     * Pattern that will be applied for password.
     *
     * example of pattern :
     * '^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$'
     *
     * This example pattern allow user enter only:
     *
     * ^: anchored to beginning of string
     * \S*: any set of characters
     * (?=\S{8,}): of at least length 8
     * (?=\S*[a-z]): containing at least one lowercase letter
     * (?=\S*[A-Z]): and at least one uppercase letter
     * (?=\S*[\d]): and at least one number
     * $: anchored to the end of the string
     *
     * @var string
     */
    public $passwordRegexp = '/^\S*(?=\S{6,})\S*$/';    // at least 6 characters

    /**
     * @var string Text hint displayed with password inputs; description of $passwordRegexp
     */
    public $passwordHint = 'At least 6 characters';

    /**
     * @var string name of Role assigned to new User
     */
    public $standardRole = 'visitor';

    /**
     * @var string
     */
    public $ruleNamespace = 'app\rbac';

    /**
     * @var int seconds that a token remains valid. Default is six hours.
     */
    public $tokenStamina = 21600;

    /**
     * @var int seconds that a cookie-based login remains valid. Default is thirty days.
     */
    public $loginStamina = 2592000;

    /**
     * @var bool whether a user can have multiple roles
     */
    public $multipleRoles = false;

    /**
     * @var string  form class used in dialogs; if null, is set to bootstrap ActiveForm
     */
    public $formClass;

    /**
     * @var bool    if true, puts the whole site behind a 'fence'
     */
    public $fenceMode = false;

    /**
     * @var string  yii\db\BaseActiveRecord class name; used as profile
     */
    public $profileClass;

    /**
     * @var string  the class name of the identity object associated with the current user
     * May be changed into a class extended from sjaakp\pluto\models\User
     */
    public $identityClass = 'sjaakp\pluto\models\User';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (! Yii::$app->has('authManager'))    {
            throw new InvalidConfigException('$app::authManager is not configured.');
        }
        parent::init();

        if (! isset( Yii::$app->i18n->translations['pluto']))   {
            Yii::$app->i18n->translations['pluto'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@sjaakp/pluto/messages',
            ];
        }

        if (empty($this->formClass)) $this->formClass = $this->bootstrapNamespace() . '\ActiveForm';
    }

    /**
     * @param $action
     * @return int
     */
    public function getPwFlags($action)
    {
        return ($this->passwordFlags['all'] ?? 0) | ($this->passwordFlags[$action] ?? 0);
    }

    /**
     * @param $event  UserEvent
     */
    public static function beforeLogin($event)
    {
        /* @var $user User */
        $user = $event->identity;
        if ($user->status != User::STATUS_ACTIVE) $event->isValid = false;  // holds blocked user
        else    {
            $user->touch('lastlogin_at');
            $user->updateCounters(['login_count' => 1]);
        }
    }

    /**
     * @param $rule
     * @param $action
     * @return yii\web\Response
     * @throws yii\web\ForbiddenHttpException
     */
    public static function accessDenied($rule, $action)
    {
        return self::denyAccess($action->controller);
    }

    /**
     * @param $controller yii\web\Controller
     * @param null $message string
     * @param array $messageKey string
     * @return yii\web\Response
     * @throws yii\web\ForbiddenHttpException
     */
    public static function denyAccess($controller, $message = null, $messageKey = 'danger')
    {
        $user = Yii::$app->user;
        if ($user !== false && $user->isGuest) {
            return $user->loginRequired();
        } else {
            /* @var $identity User */
            $identity = $user->identity;
            if (! $message) {
                $elmnts = $controller->module === Yii::$app ? [] : [ $controller->module->id ];
                $elmnts[] = $controller->id;
                $elmnts[] = $controller->action->id;

                $message = Yii::t('pluto','Sorry {username}, you\'re not allowed to visit <strong>{route}</strong> on this site.', [
                    'username' => $identity->name ?? '',
                    'route' => implode('/', $elmnts)
                ]);
            }
            Yii::$app->session->setFlash($messageKey, $message);
            $r = $controller->goBack();
            Yii::$app->user->setReturnUrl(null);
            return $r;
        }
    }

    /**
     * {@inheritdoc}
     *
     */
    public function bootstrap($app)
    {
        if ($app instanceof WebApplication) {
            $rules = new GroupUrlRule([
                'prefix' => $this->id,
                'rules' => [
                    '<a:(confirm|recover)>/<token:[A-Za-z0-9_-]+>' => 'default/<a>',
                    '<a:[\w\-]+>/<id:\d+>' => 'default/<a>',
                    '<c:[\w\-]+>/<a:[\w\-]+>/<id:[\w\-]+>' => '<c>/<a>',
                    '<c:(user|role|permission)>' => '<c>/index',
                    '<a:[\w\-]+>' => 'default/<a>',
                ]
            ]);
            $app->getUrlManager()->addRules([$rules], false);

            $app->setComponents([
                'user' => ArrayHelper::merge($app->components['user'], [
                    'identityClass' => $this->identityClass,
                    'loginUrl' => [$this->id . '/default/login'],
                    'on beforeLogin' => [$this, 'beforeLogin']
                ]),
            ]);

            if ($this->fenceMode)   {
                $app->on(WebApplication::EVENT_BEFORE_ACTION, function($event) {
                    $user = Yii::$app->user;
                    if ($user->isGuest)   {
                        $action = $event->action->id;
                        if ($event->action->controller->module->id != $this->id ||
                            ! in_array($action, ['login', 'error', 'forgot', 'recover']))   {
                            $event->isValid = false;
                            return $user->loginRequired();
                        }
                    }
                    return null;
                });
            }
        } else {
            /* @var $app ConsoleApplication */

            $app->controllerMap = ArrayHelper::merge($app->controllerMap, [
                'migrate' => [
                    'class' => '\yii\console\controllers\MigrateController',
                    'migrationNamespaces' => [
                        'sjaakp\pluto\migrations'
                    ]
                ],
                'pluto' => 'sjaakp\pluto\commands\PlutoController'
            ]);
        }
    }

    /**
     * @return string the namespace of the Bootstrap extension ('yii\bootstrap' or 'yii\bootstrap4')
     * @throws InvalidConfigException
     */
    public function bootstrapNamespace()
    {
        foreach ([ '4', '3', ''] as $v)  {
            $ns = 'yii/bootstrap' . $v;
            if (strrpos(Yii::getAlias( '@' . $ns, false),'/src') !== false) return str_replace('/', '\\', $ns);
        }
        throw new InvalidConfigException( 'No Bootstrap extension found');
    }
}
