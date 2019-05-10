<?php

namespace sjaakp\pluto\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use sjaakp\pluto\models\Rule;

class RuleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'permissions' => ['manageRoles'],
                    ],
                ],
                'denyCallback' => [ 'sjaakp\pluto\Module', 'accessDenied' ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionIndex()
    {
        $namespace = $this->module->ruleNamespace;

        $files = FileHelper::findFiles(Yii::getAlias('@' . str_replace('\\', '/', $namespace)), [ 'only' => ['*Rule.php'] ]);

        $fileRules = [];
        foreach ($files as $path)   {
            $rule = Yii::createObject($namespace . '\\' . basename($path, '.php'));
            $fileRules[$rule->name] = $rule;
        }

        $auth = Yii::$app->authManager;

        $unregistered = array_diff_key($fileRules, $auth->getRules());

        if (Yii::$app->request->isPost) {
            foreach($unregistered as $rule) {
                /* @var $rule yii\rbac\Rule */
                $auth->add($rule);
            }
            $unregistered = [];
        }

        $regData = new ArrayDataProvider([
            'allModels' => $auth->getRules(),
        ]);
        $unregData = new ArrayDataProvider([
            'allModels' => $unregistered,
        ]);
        return $this->render('index', [
            'regData' => $regData,
            'unregData' => $unregData,
        ]);
    }

    /**
     * Deletes Rule from RBAC-system, does not delete Rule's class file
     * @param $id
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = new Rule($id);
        $model->delete();

        return $this->redirect(['index']);
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
}
