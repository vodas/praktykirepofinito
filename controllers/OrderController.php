<?php

namespace app\controllers;

use app\components\ErrorHelper;
use Yii;
use app\models\Order;
use app\models\OrderCRUD;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3&&Yii::$app->user->identity->user_role!=2&&Yii::$app->user->identity->user_role!=1)) {
            return $this->goHome();
        }
        $searchModel = new OrderCRUD();
        $searchModel->scenario = Order::SCENARIO_READ;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3&&Yii::$app->user->identity->user_role!=2&&Yii::$app->user->identity->user_role!=1)) {
            return $this->goHome();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    private function createAjax() {
        $data = Yii::$app->request->post();
        $model = new Order(['scenario' => Order::SCENARIO_CREATE]);

        foreach ($data['Order'] as $key => $value) {
            $model->$key = $value;
        }
        if ($model->validate()) {
            return $model->save() ? true : ErrorHelper::errorsToString($model->errors);
        } else {
            return ErrorHelper::errorsToString($model->errors);
        }
    }
    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->user->isGuest || Yii::$app->user->identity->user_role!=4) {
            return $this->goHome();
        } else {
            if(Yii::$app->request->isAjax) {
                return $this->createAjax();
            } else {
                $model = new Order(['scenario' => Order::SCENARIO_CREATE]);
                return $this->render('create', [
                    'model' => $model,
                ]);

            }
        }
    }

    private function updateAjax() {
        $data = Yii::$app->request->post();
        $model = Order::findOne(['order_id' => $_GET['id']]);
        if($model != null) {
            $model->scenario = Order::SCENARIO_UPDATE;
            foreach ($data['Order'] as $key => $value) {
                $model->$key = $value;
            }
            if ($model->validate()) {
                return $model->save() ? true : ErrorHelper::errorsToString($model->errors);
            } else {
                return ErrorHelper::errorsToString($model->errors);
            }
        }
    }
    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->user->isGuest || Yii::$app->user->identity->user_role!=4) {
            return $this->goHome();
        } else {
            if(Yii::$app->request->isAjax) {
                return $this->updateAjax();
            } else {
                $model = $this->findModel($id);
                $model->scenario = Order::SCENARIO_UPDATE;
                return $this->render('update', [
                    'model' => $model,
                ]);

            }
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3&&Yii::$app->user->identity->user_role!=2&&Yii::$app->user->identity->user_role!=1)) {
            return $this->goHome();
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model=new Order();
        $model->scenario = Order::SCENARIO_READ;
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
