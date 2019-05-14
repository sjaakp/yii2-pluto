<?php
/**
 * yii2-pluto
 * ----------
 * User management module for Yii2 framework
 * Version 1.0.0
 * Copyright (c) 2019
 * Sjaak Priester, Amsterdam
 * MIT License
 * https://github.com/sjaakp/yii2-pluto
 * https://sjaakpriester.nl
 */

namespace sjaakp\pluto\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use sjaakp\pluto\models\Role;
use sjaakp\pluto\models\User;

class RoleController extends Controller
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
     */
    public function actionIndex()
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' =>Yii::$app->authManager->getRoles(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new Role();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'rules' => $this->getRules(),
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionUpdate($id)
    {
        $model = new Role($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        $userIds = Yii::$app->authManager->getUserIdsByRole($model->name);

        $users = new ActiveDataProvider([
            'query' => User::find()->where(['in', 'id', $userIds])
        ]);

        return $this->render('update', [
            'model' => $model,
            'rules' => $this->getRules(),
            'users' => $users
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = new Role($id);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return array
     */
    protected function getRules()
    {
        return ArrayHelper::map(Yii::$app->authManager->getRules(), 'name', 'name');
    }
}
