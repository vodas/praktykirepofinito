<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */

$this->title = $model->warehouse_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Warehouses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', $this->title);
?>
<div class="warehouse-view">

    <h1><?= Yii::t('app', Html::encode($this->title)) ?></h1>

    <p>
        <?php if(Yii::$app->user->identity->user_role==4 || Yii::$app->user->identity->user_role==3 || Yii::$app->user->identity->user_role==2) :?>

        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->warehouse_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->warehouse_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'description',
            'price',
        ],
    ]) ?>

</div>
