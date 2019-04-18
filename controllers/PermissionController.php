<?php

namespace sjaakp\pluto\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use sjaakp\pluto\models\Permission;

class PermissionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
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
            'allModels' =>Yii::$app->authManager->getPermissions(),
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
        $model = new Permission();

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
        $model = new Permission($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'rules' => $this->getRules(),
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = new Permission($id);
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

    /**
     * @return array
     */
    protected function getRules()
    {
        return ArrayHelper::map(Yii::$app->authManager->getRules(), 'name', 'name');
    }
}
