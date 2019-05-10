<?php

namespace sjaakp\pluto\controllers;

use Yii;
use yii\base\InvalidCallException;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use sjaakp\pluto\Module;
use sjaakp\pluto\models\User;
use sjaakp\pluto\forms\LoginForm;
use sjaakp\pluto\forms\EmailForm;
use sjaakp\pluto\forms\PwChangeForm;


/**
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'settings', 'pw-change', 'delete', 'download'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm([
            'flags' => $this->module->getPwFlags('login')
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionSignup()
    {
        $model = new User([
            'scenario' => 'signup',
            'status' => User::STATUS_PENDING,
            'roles' => [$this->module->standardRole],
            'flags' => $this->module->getPwFlags('signup')
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($model->sendTokenEmail(Yii::t('pluto', 'Account registration at {appname}', [
                'appname' => Yii::$app->name
            ]), 'confirm')) {
                Yii::$app->session->setFlash('success',
                    Yii::t('pluto', 'Thank you for registration. Please check your inbox for verification email.'));
                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $token
     * @return yii\web\Response
     */
    public function actionConfirm($token)
    {
        return $this->tokenAction($token,
            Yii::t('pluto', 'Your registration has been confirmed. You are now logged in.'),
            Yii::t('pluto', 'Sorry, we are unable to verify your registration.'));
    }

    /**
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionForgot()
    {
        $model = new EmailForm([
            'flags' => $this->module->getPwFlags('forgot')
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findByEmail($model->email);
            if ($user)  {
                $user->generateToken();
                if ($user->save() && $user->sendTokenEmail(Yii::t('pluto', 'Password reset for {appname}', [
                        'appname' => Yii::$app->name
                    ]), 'recover')) {
                    Yii::$app->session->setFlash('success',
                        Yii::t('pluto', 'Please check your inbox for further instructions.'));
                    return $this->goHome();
                }
            } else {
                Yii::$app->session->setFlash('error',
                    Yii::t('pluto', 'Sorry, we are unable to reset password for this email address.'));
            }
        }

        return $this->render('forgot', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $token
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionRecover($token)
    {
        $model = User::findByToken($token);

        if ($model)  {
            $model->scenario = 'recover';
            $model->flags = $this->module->getPwFlags('recover');

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->removeToken();

                if ($model->save(false))  {
                    if (Yii::$app->user->login($model)) {
                        Yii::$app->session->setFlash('success',
                            Yii::t('pluto', 'New password saved. You are now logged in.'));

                        return $this->goHome();
                    }
                }
            }
            return $this->render('recover', [
                'model' => $model,
            ]);
        }
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionResend()
    {
        $model = new EmailForm([
            'status' => User::STATUS_PENDING,
            'flags' => $this->module->getPwFlags('resend')
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findByEmail($model->email, User::STATUS_PENDING);
            if ($user && $user->sendTokenEmail(Yii::t('pluto', 'Account registration at {appname}', [
                    'appname' => Yii::$app->name
                ]), 'confirm'))  {
                Yii::$app->session->setFlash('success',
                    Yii::t('pluto', 'Please check your inbox for verification email.'));
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error',
                Yii::t('pluto', 'Sorry, we are unable to resend a verification email for this email address.'));
        }

        return $this->render('resend', [
            'model' => $model,
        ]);
    }

    /**
     * User settings.
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionSettings()
    {
        $user = $this->getCurrentUser();
        $user->scenario = 'settings';
        $user->flags = $this->module->getPwFlags('settings');

        $req = Yii::$app->request;
        if ($req->isGet) Url::remember($req->referrer);

        if ($user->load($req->post()))   {
            $emailChanged = $user->isAttributeChanged('email');
            if ($emailChanged) $user->status = User::STATUS_PENDING;
            if ($user->save()) {
                if ($emailChanged)  {
                    $user->sendTokenEmail(Yii::t('pluto', 'Confirm email changed at {appname}', [
                        'appname' => Yii::$app->name
                    ]), 'email-changed');
                    Yii::$app->session->setFlash('success',
                        Yii::t('pluto', 'Please check your inbox for verification email.'));
                }
                else Yii::$app->session->setFlash('success', Yii::t('pluto', 'Settings saved.'));
                return $this->goBack();
            }
        }

        return $this->render( 'settings', [
            'model' => $user,
        ]);
    }

    /**
     * @param string $token
     * @return yii\web\Response
     */
    public function actionEmailChanged($token)
    {
        return $this->tokenAction($token,
            Yii::t('pluto', 'Your email has been confirmed. Settings saved.'),
            Yii::t('pluto', 'Sorry, we are unable to verify your email.'));
    }

    /**
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionPwChange()
    {
        $flags = $this->module->getPwFlags('pw-change');
        $user = $this->getCurrentUser();
        $user->scenario = 'pw-change';
        $user->flags = $flags;

        $model = new PwChangeForm([
            'user' => $user,
            'flags' => $flags
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user->save(false)) Yii::$app->session->setFlash('success',
                Yii::t('pluto', 'New password saved.'));
            else  Yii::$app->session->setFlash('error',
                Yii::t('pluto', 'Sorry, we are unable to change your password.'));
            return $this->goBack();
        }

        return $this->render('pw_change', [
            'model' => $model,
        ]);
    }

    /**
     * @return mixed
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        $model = $this->getCurrentUser();
        $model->scenario = 'delete';
        $model->flags = $this->module->getPwFlags('delete');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->user->logout();
            $model->delete();

            return $this->goHome();
        }

        return $this->render('delete', [
            'model' => $model,
        ]);
    }

    /**
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDownload()
    {
        $model =  $this->getCurrentUser();
        $model->scenario = 'download';
        $model->flags = $this->module->getPwFlags('download');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $attrs = $model->getAttributes(null, [ 'id', 'auth_key', 'password_hash', 'token', 'status' ]);
            $attrs['statusText'] = $model->statusText;
            $csv = [];
            foreach ($attrs as $key => $value)  {
                $label = $model->getAttributeLabel($key);
                $csv[] = "$label = $value";
            }
            $roles = implode(', ', $model->roles);
            $csv[] = "Role(s) = $roles";
            $appName = Yii::$app->name;
            $now = date('Y-m-d H:i:s');
            $line = str_repeat('=', 32);
            $content = "User data at $appName\r\n$line\r\nGenerated at $now\r\n\r\n" . implode("\r\n", $csv);
            return Yii::$app->response->sendContentAsFile($content, "$appName.txt", [
                'mimeType' => 'text/plain',
            ]);
        }

        return $this->render('download', [
            'model' => $model,
        ]);
    }

    /**
     * Give user a chance to override view
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render($view, $params = [])
    {
        $vw = $this->module->views[$this->id][$view] ?? $view;
        return parent::render($vw, $params);
    }

    /**
     * @param $token
     * @param $successFlash
     * @param $errorFlash
     * @return \yii\web\Response
     */
    protected function tokenAction($token, $successFlash, $errorFlash)  {
        $user = User::findByToken($token, User::STATUS_PENDING);
        if ($user)  {
            $user->removeToken();
            $user->status = User::STATUS_ACTIVE;
            if ($user->save(false)) {
                if (Yii::$app->user->login($user)) {
                    Yii::$app->session->setFlash('success', $successFlash);
                    return $this->goHome();
                }
            }
        }

        Yii::$app->session->setFlash('error', $errorFlash);
        return $this->goHome();
    }

    /**
     * @return User|null
     */
    protected function getCurrentUser()
    {
        $user = Yii::$app->user;
        if ($user->isGuest) {
            throw new InvalidCallException(Yii::t('pluto', 'User is not logged in.'));
        }
        /* @var $r User */
        $r = $user->identity;
        return $r;
    }
}
