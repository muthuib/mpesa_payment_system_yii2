/* @var $model app\models\FormData */

?>

<h1>Form Data</h1>

<p><strong>Name:</strong> <?= $model->user_name ?></p>
<p><strong>Email:</strong> <?= $model->email ?></p>
<p><strong>Message:</strong> <?= nl2br($model->message) ?></p>
<p><strong>Date:</strong> <?= date('Y-m-d H:i:s') ?></p>