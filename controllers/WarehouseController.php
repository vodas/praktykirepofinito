<?php

namespace app\controllers;

use app\components\ErrorHelper;
use Yii;
use app\models\Warehouse;
use app\models\WarehouseCRUD;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\FileUpload;
use yii\web\UploadedFile;

/**
 * WarehouseController implements the CRUD actions for Warehouse model.
 */
class WarehouseController extends Controller
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
     * Lists all Warehouse models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3&&Yii::$app->user->identity->user_role!=2)) {
            return $this->goHome();
        }
        $searchModel = new WarehouseCRUD();
        $searchModel->scenario = Warehouse::SCENARIO_READ;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Warehouse model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    private function createAjax() {
        $data = Yii::$app->request->post();
        $model = new Warehouse(['scenario' => Warehouse::SCENARIO_CREATE]);

        foreach ($data['Warehouse'] as $key => $value) {
            $model->$key = $value;
        }
        if ($model->validate()) {
            return $model->save() ? true : ErrorHelper::errorsToString($model->errors);
        } else {
            return ErrorHelper::errorsToString($model->errors);
        }
    }
    /**
     * Creates a new Warehouse model.
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
                $model = new Warehouse(['scenario' => Warehouse::SCENARIO_CREATE]);
                return $this->render('create', [
                    'model' => $model,
                ]);

            }
        }
    }

    private function updateAjax() {
        $data = Yii::$app->request->post();
        $model = Warehouse::findOne(['warehouse_id' => $_GET['id']]);
        if($model != null) {
            $model->scenario = Warehouse::SCENARIO_UPDATE;
            foreach ($data['Warehouse'] as $key => $value) {
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
     * Updates an existing Warehouse model.
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
                $model->scenario = Warehouse::SCENARIO_UPDATE;
                return $this->render('update', [
                    'model' => $model,
                ]);

            }
        }
    }

    /**
     * Deletes an existing Warehouse model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3&&Yii::$app->user->identity->user_role!=2)) {
            return $this->goHome();
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Warehouse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Warehouse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = new Warehouse();
        $model->scenario = Warehouse::SCENARIO_READ;
        if (($model = Warehouse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionAddfromxml()
    {
        if(Yii::$app->user->isGuest||(Yii::$app->user->identity->user_role!=4&&Yii::$app->user->identity->user_role!=3&&Yii::$app->user->identity->user_role!=2)) {
            return $this->goHome();
        }
        $model = new FileUpload();

        if (Yii::$app->request->isPost) {
            $model->xmlFile = UploadedFile::getInstance($model, 'xmlFile');
            if ($model->upload()) {
            // file is uploaded successfully
                Yii::$app->session->setFlash('contactFormSubmitted');
               // return $this->refresh();
               return $this->redirect(['index']);
            }
        } else {
            return $this->render('addfromxml', ['model' => $model]);
        }
    }

}
