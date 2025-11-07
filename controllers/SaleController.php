<?php
namespace app\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use app\models\Sale;
use Yii;

class SaleController extends ActiveController
{
    public $modelClass = 'app\models\Sale';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Force JSON response
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    // Handle image uploads
    public function actionUpload($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $image = UploadedFile::getInstanceByName('image');
        if (!$image) {
            return ['success' => false, 'error' => 'No image provided (field name: image)'];
        }

        $uploadPath = Yii::getAlias('@webroot') . '/uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = uniqid('sale_', true) . '.' . $image->extension;
        $fullPath = $uploadPath . '/' . $filename;

        if ($image->saveAs($fullPath)) {
            $relativePath = '/uploads/' . $filename;

            if ($id) {
                $model = Sale::findOne($id);
                if ($model) {
                    $model->image = $relativePath;
                    $model->save(false);
                }
            }

            return ['success' => true, 'image' => $relativePath];
        }

        return ['success' => false, 'error' => 'Failed to save file'];
    }

    // ✅ DELETE /sales/{id}
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Sale::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Sale not found");
        }

        if ($model->delete()) {
            return ['success' => true, 'message' => 'Sale deleted successfully'];
        }

        return ['success' => false, 'error' => 'Failed to delete sale'];
    }
}
