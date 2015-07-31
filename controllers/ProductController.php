<?php

namespace app\controllers;

use app\components\ErrorHelper;
use Yii;
use app\models\Product;
use app\models\ProductCRUD;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\ProductCRUDContainer;
use app\models\CRUDInterface;
use yii\di\Container;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    protected $CRUDService;

    public function __construct($id, $module, CRUDInterface $CRUDService, $config = [])
    {
        $this->CRUDService = $CRUDService;
        parent::__construct($id,$module,$config);
    }

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
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3)) {
            return $this->goHome();
        }

      //  $searchModel = new ProductCRUD();
      //  $searchModel->scenario = Product::SCENARIO_READ;
        $searchModel = $this->CRUDService->index();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {

        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3)) {
            return $this->goHome();
        }


        return $this->render('view', [
            'model' => $this->CRUDService->findModel($id),
        ]);
    }

    private function createAjax() {
        $data = Yii::$app->request->post();
        $model = new Product(['scenario' => Product::SCENARIO_CREATE]);

        foreach ($data['Product'] as $key => $value) {
            $model->$key = $value;
        }
        if ($model->validate()) {
            return $model->save() ? true : ErrorHelper::errorsToString($model->errors);
        } else {
            return ErrorHelper::errorsToString($model->errors);
        }
    }
    /**
     * Creates a new Product model.
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
                $model = new Product(['scenario' => Product::SCENARIO_CREATE]);
                return $this->render('create', [
                    'model' => $model,
                ]);

            }
        }
    }
    private function updateAjax() {
        $data = Yii::$app->request->post();
        $model = Product::findOne(['product_id' => $_GET['id']]);
        if($model != null) {
            $model->scenario = Product::SCENARIO_UPDATE;
            foreach ($data['Product'] as $key => $value) {
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
     * Updates an existing Product model.
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
                $model = Product::findOne(['product_id' => $id]);
                $model->scenario = Product::SCENARIO_UPDATE;
                return $this->render('update', [
                    'model' => $model,
                ]);

            }
        }
    }
    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3)) {
            return $this->goHome();
        }
        $this->CRUDService->delete($id);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

}
