<?php
namespace app\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;
use yii\filters\auth\HttpBearerAuth;
use app\models\Sale;
use Yii;

class SaleController extends ActiveController
{
    public $modelClass = 'app\models\Sale';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Force JSON response
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Add authentication filter
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['index', 'view'], // Allow viewing without auth
        ];

        return $behaviors;
    }

    /**
     * Override create action to set user_id from authenticated user
     */
    public function actionCreate()
    {
        // Verify authentication first
        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new UnauthorizedHttpException('Authentication required. Please provide a valid access token in the Authorization header.');
        }
        
        if (!$user->id || $user->id <= 0) {
            throw new UnauthorizedHttpException('Invalid user identity.');
        }
        
        // Use getBodyParams() for JSON requests instead of post()
        $data = Yii::$app->request->getBodyParams();
        
        // Remove user_id from POST data if present (security: prevent user from setting their own user_id)
        unset($data['user_id']);
        
        // Add user_id to data array BEFORE creating model so it's included during load()
        $data['user_id'] = (int)$user->id;
        
        $model = new Sale();
        
        // Load the data (user_id is now in $data so it will be loaded)
        $model->load($data, '');
        
        // Ensure user_id is set (safety check)
        if (empty($model->user_id) || $model->user_id <= 0) {
            $model->user_id = (int)$user->id;
        }
        
        // Validate before saving to get better error messages
        if (!$model->validate()) {
            Yii::$app->response->setStatusCode(422);
            $errors = [];
            foreach ($model->errors as $field => $messages) {
                foreach ($messages as $message) {
                    $errors[] = [
                        'field' => $field,
                        'message' => $message
                    ];
                }
            }
            return $errors;
        }

        if ($model->save(false)) { // false = skip validation since we already validated
            Yii::$app->response->setStatusCode(201);
            return $model;
        }
        
        throw new \yii\web\ServerErrorHttpException('Failed to create sale');
    }

    /**
     * Override update action to verify ownership
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        // Verify ownership
        $user = Yii::$app->user->identity;
        if (!$user || $model->user_id !== $user->id) {
            throw new ForbiddenHttpException('You can only edit your own sales');
        }

        // Work with JSON payloads and block manual user_id tampering
        $data = Yii::$app->request->getBodyParams();
        unset($data['user_id']);

        $model->load($data, '');

        if (!$model->validate()) {
            Yii::$app->response->setStatusCode(422);
            return $model->errors;
        }
        
        if ($model->save(false)) {
            return $model;
        }
        
        throw new \yii\web\ServerErrorHttpException('Failed to update sale');
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
                    // Verify ownership if updating existing sale
                    $user = Yii::$app->user->identity;
                    if (!$user || $model->user_id !== $user->id) {
                        // Delete uploaded file if unauthorized
                        @unlink($fullPath);
                        throw new ForbiddenHttpException('You can only upload images to your own sales');
                    }
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

        $model = $this->findModel($id);
        
        // Verify ownership
        $user = Yii::$app->user->identity;
        if (!$user || $model->user_id !== $user->id) {
            throw new ForbiddenHttpException('You can only delete your own sales');
        }

        if ($model->delete()) {
            return ['success' => true, 'message' => 'Sale deleted successfully'];
        }

        return ['success' => false, 'error' => 'Failed to delete sale'];
    }

    /**
     * Finds the Sale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested sale does not exist.');
    }
}
