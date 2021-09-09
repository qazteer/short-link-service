<?php

namespace app\controllers;

use app\models\Links;
use app\models\LinksTypesSearch;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LinksController implements the CRUD actions for Links model.
 */
class LinksController extends Controller
{
    protected static $chars = "abcdfghjkmnpqrstvwxyz|ABCDFGHJKLMNPQRSTVWXYZ|0123456789";
    protected static $checkUrlExists = true;
    protected static $codeLength = 8;

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Lists all Links models.
     * @return mixed
     * @throws Exception
     */
    public function actionIndex()
    {
        $model = new Links();
        $searchModel = new LinksTypesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $shortCode = $this->actionCreate($model);
                if(!$shortCode) {
                    return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'model' => $model,
                    ]);
                }
                $model->short_code = $shortCode;

                if($model->save()) {
                    Yii::$app->session->setFlash('success', "Link saved successfully");
                    return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'model' => $model,
                    ]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        if ($token = $this->request->get('token')) {
            //print_r($token);exit();
            $url = $this->shortCodeToUrl($token);
            return Yii::$app->getResponse()->redirect($url);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * @param $model
     * @return bool|string
     * @throws Exception
     */
    public function actionCreate($model)
    {
        if($this->validateUrlFormat($model->long_url) == false){
            throw new Exception(Yii::$app->params['link_not_valid_format']);
        }

        if(self::$checkUrlExists){
            if (!$this->verifyUrlExists($model->long_url)){
                throw new Exception(Yii::$app->params['url_not_exist']);
            }
        }

        $shortCode = $this->urlExistsInDB($model->long_url);

        if($shortCode == false){
            return $this->createShortCode(self::$codeLength);
        }

        Yii::$app->session->setFlash('error', "This link already exists in the database");
        return false;
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function validateUrlFormat($url){
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param $url
     * @return bool
     */
    protected function verifyUrlExists($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }

    /**
     * @param $url
     * @return bool|mixed
     */
    protected function urlExistsInDB($url){
        $link = Links::find()->where(['long_url' => $url])->one();

        return (empty($link->short_code)) ? false : $link->short_code;
    }

    /**
     * @param int $length
     * @return string
     */
    protected function createShortCode($length = 8){
        $sets = explode('|', self::$chars);
        $all = '';
        $randString = '';
        foreach($sets as $set){
            $randString .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++){
            $randString .= $all[array_rand($all)];
        }
        $randString = str_shuffle($randString);
        return $randString;
    }

    /**
     * @param $short_code
     * @return bool|mixed
     * @throws NotFoundHttpException
     */
    public function shortCodeToUrl($short_code){

        $model = Links::find()->where(['short_code' => $short_code])->one();
        $lifetime = strtotime($model->created_dt) + ($model->lifetime * 60);

        if(($model->limit != 0) && (($model->hits + 1) > $model->limit)) throw new NotFoundHttpException(Yii::$app->params['not_valid_link']);

        if(time() > $lifetime) throw new NotFoundHttpException(Yii::$app->params['link_expired']);

        if(empty($model)) return false;

        $model->hits = $model->hits + 1;
        $model->save();

        return $model->long_url;
    }





//
//    /**
//     * Displays a single Links model.
//     * @param int $id ID
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }
//
//
//
//    /**
//     * Updates an existing Links model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param int $id ID
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        }
//
//        return $this->render('update', [
//            'model' => $model,
//        ]);
//    }
//
//    /**
//     * Deletes an existing Links model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param int $id ID
//     * @return mixed
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }
//
//    /**
//     * Finds the Links model based on its primary key value.
//     * If the model is not found, a 404 HTTP exception will be thrown.
//     * @param int $id ID
//     * @return Links the loaded model
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    protected function findModel($id)
//    {
//        if (($model = Links::findOne($id)) !== null) {
//            return $model;
//        }
//
//        throw new NotFoundHttpException('The requested page does not exist.');
//    }
}
