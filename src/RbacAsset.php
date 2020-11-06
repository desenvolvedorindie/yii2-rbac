<?php

namespace desenvolvedorindie\rbac;

use yii\web\AssetBundle;

class RbacAsset extends AssetBundle
{
    public $sourcePath = '@desenvolvedorindie\rbac\assets';

    public $css = [
        'css/main.css',
    ];
    public $js = [];
    public $depends = [
        //add a dependencia do adminlte
    ];

}