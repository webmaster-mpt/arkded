<?php

namespace backend\controllers;

use backend\models\Aboba;
use backend\models\AbobaSearch;
use common\models\LoginForm;
use common\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','logout'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [ 'index'],
                        'allow' => true,
                        'roles' => ['?'],
                        'denyCallback' => function($rule, $action) {
                            return $this->redirect(Url::toRoute(['/site/login']));
                        }
                    ],
                    [
                        'actions' => [ 'index','filter-code-buyer','filter-code-rooms','filter-code-homes'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            /** @var User $user */
                            $user = Yii::$app->user->getIdentity();
                            return $user->isAdmin() || $user->isModer();
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
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
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $users = User::find()->where(['user.role'=>'1'])->all();
        $user = Yii::$app->user->getIdentity();
        if (Yii::$app->user->getIdentity()->isModer()) {
            return $this->redirect(['/user/index-moder', 'id' => $user]);
        }
        $abobaSearchModel = new AbobaSearch();
        $abobaDataProvider = $abobaSearchModel->search($this->request->queryParams);
        return $this->render('index', [
            'users'=>$users,
            'abobaSearchModel'=>$abobaSearchModel,
            'abobaDataProvider' =>$abobaDataProvider
        ]);
    }

//    public function actionFilterCodeBuyer()
//    {
//        $abobaSearchModel = new AbobaSearch();
//        $abobaDataProvider = $abobaSearchModel->searchBuyer($this->request->queryParams);
//        return $this->render('FilterCodeBuyer', [
//            'abobaSearchModel'=>$abobaSearchModel,
//            'abobaDataProvider' =>$abobaDataProvider
//        ]);
//    }
//
//    public function actionFilterCodeHomes()
//    {
//        $abobaSearchModel = new AbobaSearch();
//        $abobaDataProvider = $abobaSearchModel->searchHomes($this->request->queryParams);
//        return $this->render('FilterCodeHomes', [
//            'abobaSearchModel'=>$abobaSearchModel,
//            'abobaDataProvider' =>$abobaDataProvider
//        ]);
//    }
//
//    public function actionFilterCodeRooms()
//    {
//        $abobaSearchModel = new AbobaSearch();
//        $abobaDataProvider = $abobaSearchModel->searchRooms($this->request->queryParams);
//        return $this->render('FilterCodeRooms', [
//            'abobaSearchModel'=>$abobaSearchModel,
//            'abobaDataProvider' =>$abobaDataProvider
//        ]);
//    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
