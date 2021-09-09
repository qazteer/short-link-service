<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LinksTypesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Links';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="links-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'long_url:url',
            [
                'label' => Yii::t('app', 'Short Code'),
                'attribute' => 'short_code',
                'format' => 'raw',
                'value' => function ($model) {
                    $value = Yii::t('app', $model->short_code);
                    $url = Url::toRoute(['/links', 'token' => $model->short_code]);
                    return Html::a($model->short_code, $url,
                        ['target' => '_blank']);
                }
            ],
            'limit',
            //'hits',
            'lifetime',
            //'created_dt',
            //'updated_dt',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
