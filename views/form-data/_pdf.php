<?php
/* @var $model app\models\FormData */
?>

<h1>Appointment Letter</h1>

<!-- Align User Name and Email to the right -->
<p style="text-align: right;"><strong>User Name:</strong> <?= $model->user_name ?></p>
<p style="text-align: right;"><strong>Email:</strong> <?= $model->email ?></p>

<!-- Message Section -->
<p><strong>Message:</strong></p>
<p><?= nl2br($model->message) ?></p>

<!-- Date Submitted -->
<p><strong>Date Submitted:</strong> <?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>