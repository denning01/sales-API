<?php
namespace app\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use Yii;

class PageController extends ActiveController
{
    public $modelClass = 'app\models\Page';

    public function behaviors()
    {
        $b = parent::behaviors();
        $b['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $b;
    }

    // allow GET by slug, e.g. GET /pages/landing or /pages/about
    public function actionViewBySlug($slug)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->modelClass::findOne(['slug' => $slug]);
        if (!$model) {
            return ['success' => false, 'error' => 'Page not found'];
        }
        return $model;
    }
}
