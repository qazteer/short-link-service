<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Links */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="links-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'long_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'limit')->textInput() ?>

    <?= $form->field($model, 'lifetime')->textInput(['type' => 'number']) ?>

    <div class="form-group">
        <?= Html::submitButton('Create Link', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
