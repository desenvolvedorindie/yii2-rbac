<?php


namespace desenvolvedorindie\rbac\controllers;

use yii\web\Controller;

class RoleController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView(string $id)
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
    }
}