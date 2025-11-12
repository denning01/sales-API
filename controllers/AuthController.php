<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\User;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Allow CORS for frontend
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];

        // Return JSON by default
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    // POST /auth/register
    public function actionRegister()
    {
        $data = Yii::$app->request->getBodyParams();

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return [
                'status' => 'error',
                'message' => 'Username, email, and password are required.',
            ];
        }

        $user = new User();
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->setPassword($data['password']);
        $user->generateAuthKey();
        $user->generateAccessToken();

        try {
            if ($user->save()) {
                return [
                    'status' => 'success',
                    'message' => 'User registered successfully',
                    'user' => $user,
                ];
            } else {
                return [
                    'status' => 'error',
                    'errors' => $user->errors,
                ];
            }
        } catch (\yii\db\IntegrityException $e) {
            return [
                'status' => 'error',
                'message' => 'Username or email already exists.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage(),
            ];
        }
    }

    // POST /auth/login
    public function actionLogin()
    {
        $data = Yii::$app->request->getBodyParams();

        if (empty($data['username']) || empty($data['password'])) {
            return [
                'status' => 'error',
                'message' => 'Username and password are required.',
            ];
        }

        $user = User::findByUsername($data['username']);

        if (!$user || !$user->validatePassword($data['password'])) {
            throw new UnauthorizedHttpException('Invalid username or password.');
        }

        $user->generateAccessToken();
        $user->save(false);

        return [
            'status' => 'success',
            'access_token' => $user->access_token,
            'user' => $user,
        ];
    }
}
