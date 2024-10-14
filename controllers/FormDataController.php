<?php

namespace app\controllers;
use yii;
use app\models\FormData;
use app\models\FormDataSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use TCPDF;

/**
 * FormDataController implements the CRUD actions for FormData model.
 */
class FormDataController extends Controller
{
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
     * Lists all FormData models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FormDataSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FormData model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    
    public function actionCreate()
    {
        $model = new FormData();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Generate PDF and send email
            $this->generatePdfAndSendEmail($model);

            // Redirect to a thank you page or back to the form
            return $this->redirect(['thank-you']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    protected function generatePdfAndSendEmail($model)
    {
        // Create new PDF document using TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();

        // Create HTML content for PDF
        $html = $this->renderPartial('_pdf', ['model' => $model]);
        $pdf->writeHTML($html, true, false, true, false, '');

        // Save the PDF to a temporary file
        $pdfFilePath = Yii::getAlias('@webroot') . '/uploads/form_data_' . $model->id . '.pdf';
        $pdf->Output($pdfFilePath, 'F');

        // Send an email with the PDF attached
        Yii::$app->mailer->compose()
            ->setTo($model->email)
            ->setFrom([Yii::$app->params['adminEmail'] => 'Admin'])
            ->setSubject('Your Submitted Form Data')
            ->setTextBody('Please find the attached PDF of the form you submitted.')
            ->attach($pdfFilePath)
            ->send();

        // Delete the PDF file after sending
        @unlink($pdfFilePath);
    }

    public function actionThankYou()
    {
        return $this->render('thank-you');
    }

    /**
     * Updates an existing FormData model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FormData model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FormData model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return FormData the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FormData::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}