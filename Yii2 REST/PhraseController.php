<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Phrase;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\db\Expression;

/**
 * Phrase Controller API
 */
class PhraseController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Phrase';
    public $createScenario = 'create';
    public $updateScenario = 'update';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Adding Http Bearer Authentication
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['options', 'get-all', 'get-single-phrase']
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    /**
     * Custom action for creating new phrases
     * @return Phrase
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;

        $model = new Phrase;
        $model->scenario = $this->createScenario;

        $model->title = $request->post('title');
        $model->description = $request->post('description');
        $model->order = $request->post('order');
        $model->user_id = $request->post('user_id');

        if ($model->validate()) {
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
        }
        return $model;
    }

    /**
     * Custom action for get all phrases
     * @return Phrase
     * @throws ServerErrorHttpException
     */
    public function actionGetAll()
    {
        $request = Yii::$app->request;

        $except_ids = array();
        $accept_ids = array();

        if($request->post('except_ids') !== null) {
            $except_ids = explode(',', $request->post('except_ids'));
        }
        if($request->get('except_ids') !== null) {
            $except_ids = explode(',', $request->get('except_ids'));
        }

        if($request->post('accept_ids') !== null) {
            $accept_ids = explode(',', $request->post('accept_ids'));
        }
        if($request->get('accept_ids') !== null) {
            $accept_ids = explode(',', $request->get('accept_ids'));
        }

        $phrase = Phrase::find()->where(['not in', 'id', array_diff($except_ids, $accept_ids)])->orderBy(['id' => SORT_ASC])->limit(Yii::$app->params['recordingLimit'])->all();

        if (empty($phrase))
            throw new NotFoundHttpException('Phrase not found');

        return $phrase;
    }

    /**
     * Custom action for get single phrase
     * @return Phrase
     * @throws ServerErrorHttpException
     */
    public function actionGetSinglePhrase()
    {
        $request = Yii::$app->request;
        $phrase_id = "";
        if($request->post('phrase_id') !== null) {
            $phrase_id = explode(',', $request->post('phrase_id'));
        }
        if($request->get('phrase_id') !== null) {
            $phrase_id = explode(',', $request->get('phrase_id'));
        }

        if (empty($phrase_id))
            throw new NotFoundHttpException('Phrase ID not found');

        $phrase = Phrase::findOne($phrase_id);
        
        if (empty($phrase))
            throw new NotFoundHttpException('Phrase not found');

        return $phrase;
    }

    /**
     * Custom action for get random single phrase
     * @return Phrase
     * @throws ServerErrorHttpException
     */
    public function actionGetRandom()
    {
        $phrase = Phrase::find()->orderBy(new Expression('rand()'))->one();
        
        if (empty($phrase))
            throw new NotFoundHttpException('Phrase not found');

        return $phrase;
    }
}
