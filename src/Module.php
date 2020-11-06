<?php


namespace desenvolvedorindie\rbac;

use yii\base\BootstrapInterface;


class Module extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'desenvolvedorindie\rbac\controllers';

    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            ['class' => 'yii\web\UrlRule', 'pattern' => $this->id, 'route' => $this->id . '/default/index'],
            ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<id:\w+>', 'route' => $this->id . '/default/view'],
            ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>'],
        ], false);
    }
}