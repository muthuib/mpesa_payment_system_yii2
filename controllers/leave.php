when registry verifies first time leave application,,,,,it also verifies leave extension i want the extension to remain
and vise versa in

<?php

namespace app\controllers;

use Yii;
use app\models\search\LeaveApprovalSearch;
use app\models\search\StaffOnLeaveSearch;
use app\models\search\FtaHosSearch;
use app\models\search\FtaHodSearch;
use app\models\search\ApprovedHodSearch;
use app\models\search\FtaHodDisapprovedSearch;
use app\models\search\FtaRegistrySearch;
use app\models\LeaveApplication;
use app\models\StaffDetail;
use kartik\mpdf\Pdf;
use app\models\ApprovalHistory;
use app\models\LeaveApproval;
use app\models\LeaveTransactions;
use app\models\ApprovalLevel;
use app\models\ApprovalStatus;
use app\models\LeaveExtension;
use app\models\LeaveHolidays;
use yii\web\Controller;
use app\models\StaffSection;
use yii\data\ActiveDataProvider;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;

class LeaveApprovalController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'hos-approve', 'hod-approve', 'registry-approve'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Only authenticated users (logged in)
                    ],
                ],
            ],
        ];
    }
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        return parent::beforeAction($action);
    }

    public function actionStaffOnLeave()
    {
        $loggedInUserPayrollNo = Yii::$app->user->identity->username;

        $staffSection = StaffSection::find()
            ->where(['payroll_no' => $loggedInUserPayrollNo])
            ->one();

        if (!$staffSection) {
            return $this->render('staff_on_leave', [
                'searchModel' => new StaffOnLeaveSearch(),
                'dataProvider' => new ActiveDataProvider(['query' => LeaveApproval::find()->where('0=1')]),
            ]);
        }

        $hodDepartmentId = $staffSection->department_section_id;

        $searchModel = new StaffOnLeaveSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $hodDepartmentId);

        return $this->render('staff_on_leave', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionGeneratePdf()
    {
        // Get the logged-in user's payroll number
        $loggedInUserPayrollNo = Yii::$app->user->identity->username;

        // Find the staff section of the logged-in user
        $staffSection = StaffSection::find()
            ->where(['payroll_no' => $loggedInUserPayrollNo])
            ->one();

        // Check if the staff section is found, otherwise return an empty result
        if (!$staffSection) {
            return $this->render('error', [
                'message' => 'Staff section not found.',
            ]);
        }

        // Get the HOD department ID
        $hodDepartmentId = $staffSection->department_section_id;

        // Prepare the query with the necessary conditions
        $query = LeaveApproval::find()
            ->joinWith(['leaveApplication', 'leaveApplication.staffDetail.staffSection.departmentSection'])
            ->where(['in', 'status_id', [$this->getStatusId('APPROVED'), $this->getStatusId('VERIFIED')]])
            ->andWhere(['in', 'ls_leave_approval.level_id', [$this->getLevelId('HOD'), $this->getLevelId('REGISTRY')]])
            ->andWhere(['<=', 'ls_leave_application.start_date', date('Y-m-d')])
            ->andWhere(['>=', 'ls_leave_application.end_date', date('Y-m-d')])
            ->andWhere(['ls_staff_section.department_section_id' => $hodDepartmentId]);

        // Get the data provider with the query
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Render the HTML content using a partial view
        $content = $this->renderPartial('_pdf-report', [
            'dataProvider' => $dataProvider,
        ]);

        // Setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'content' => $content,
            'filename' => 'Staff_On_Leave_Report_' . date('Y-m-d') . '.pdf',
            'options' => [
                'title' => 'Staff on Leave Report',
            ],
            'methods' => [
                'SetHeader' => ['Staff on Leave Report'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        // Return the PDF output as a string to be sent to the browser
        return $pdf->render();
    }


    public function actionIndex()
    {

        $searchModel = new LeaveApprovalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // FTA (First Time Application) Data Providers
        $ftaHosDataProvider = new ActiveDataProvider([
            'query' => LeaveApproval::find()
                ->joinWith(['leaveApplication.leaveExtension']) // Join leaveExtension
                ->where(['level_id' => $this->getLevelId('HOS')])
                ->andWhere(['status_id' => $this->getStatusId('SUBMITTED')])
                ->andWhere(['is', 'ls_leave_extension.extension_id', null])
                ->andWhere(['<>', 'status_id', $this->getStatusId('DISAPPROVED')])
                ->andWhere(['<>', 'status_id', $this->getStatusId('REVIEW')])
                ->andWhere(['extend' => false]), // First-time applications (extend = false)
        ]);

        $ftaHodDataProvider = new ActiveDataProvider([
            'query' => LeaveApproval::find()
                ->joinWith(['leaveApplication.leaveExtension'])
                ->where(['level_id' => $this->getLevelId('HOD')])
                ->andWhere([
                    'or',
                    ['status_id' => $this->getStatusId('FORWARDED')], // Include forwarded applications
                    ['status_id' => $this->getStatusId('DISAPPROVED')] // Include disapproved applications
                ])
                ->andWhere(['<>', 'status_id', $this->getStatusId('REVIEW')])
                ->andWhere(['is', 'ls_leave_extension.extension_id', null])
                ->andWhere(['extend' => false]), // First-time applications (extend = false)
        ]);

        $approvedHodDataProvider = new ActiveDataProvider([
            'query' => LeaveApproval::find()
                ->joinWith(['leaveApplication', 'leaveExtension']) // Join with the leaveExtension model
                ->where(['in', 'status_id', [$this->getStatusId('APPROVED'), $this->getStatusId('VERIFIED')]])
                ->andWhere(['in', 'ls_leave_approval.level_id', [$this->getLevelId('HOD'), $this->getLevelId('REGISTRY')]])
                ->andWhere(['<=', 'ls_leave_application.start_date', new \yii\db\Expression('NOW()')]) // Ongoing leave: start_date <= NOW()
                ->andWhere(['>=', 'ls_leave_application.end_date', new \yii\db\Expression('NOW()')])   // Ongoing leave: end_date >= NOW()
        ]);
        

        // $ftaRegistryDataProvider = new ActiveDataProvider([
        //     'query' => LeaveApproval::find()
        //         ->joinWith(['leaveApplication.leaveExtension']) // Join leaveExtension
        //         ->where(['level_id' => $this->getLevelId('REGISTRY')])
        //         ->andWhere(['<>', 'status_id', $this->getStatusId('VERIFIED')])
        //         ->andWhere([
        //             'or',
        //             ['is', 'ls_leave_extension.extension_id', null], // No extension exists (FTA)
        //             ['IS NOT', 'ls_leave_extension.extension_id', null] // Extension exists
        //         ])
        //         // Important: Ensure that FTAs are still shown even if there's an extension
        //         ->andWhere(['extend' => false]), // Only FTAs (extend = false)
        // ]);
        $ftaRegistryDataProvider = new ActiveDataProvider([
            'query' => LeaveApproval::find()
            ->joinWith(['leaveApplication.leaveExtension']) // Join leaveExtension
            ->where(['level_id' => $this->getLevelId('REGISTRY')])
            ->andWhere(['extend' => false]) // Only first-time applications (extend = false)
            ->andWhere(['status_id' => $this->getStatusId('APPROVED')])
            ->andWhere(['is', 'ls_leave_extension.extension_id', null]) // Ensure it's not an extension
        ]);
        //dd($ftaRegistryDataProvider);


        // $records = $ftaRegistryDataProvider->getModels();
        // VarDumper::dump($records, 10, true);

        // Extension Applications Data Providers
        $extensionHosDataProvider = new ActiveDataProvider([
            'query' => LeaveApproval::find()
                ->joinWith(['leaveApplication.leaveExtension']) // Join leaveExtension
                ->where(['level_id' => $this->getLevelId('HOS')])
                ->andWhere(['IS NOT', 'ls_leave_extension.extension_id', null])
                ->andWhere(['extend' => true]), // Extension applications (extend = true)
        ]);

        $extensionHodDataProvider = new ActiveDataProvider([
            'query' => LeaveApproval::find()
                ->joinWith(['leaveApplication.leaveExtension']) // Join leaveExtension
                ->where(['level_id' => $this->getLevelId('HOD')])
                ->andWhere(['<>', 'status_id', $this->getStatusId('DISAPPROVED')])
                ->andWhere(['IS NOT', 'ls_leave_extension.extension_id', null])
                ->andWhere(['extend' => true]), // Extension applications (extend = true)
        ]);

        $extensionRegistryDataProvider = new ActiveDataProvider([
            'query' => LeaveApproval::find()
                ->joinWith(['leaveApplication.leaveExtension']) // Join leaveExtension
                ->where(['level_id' => $this->getLevelId('REGISTRY')])
                ->andWhere(['<>', 'status_id', $this->getStatusId('VERIFIED')])
                ->andWhere(['IS NOT', 'ls_leave_extension.extension_id', null])
                ->andWhere(['extend' => true]), // Extension applications (extend = true)
        ]);
      
        return $this->render('index', [
            // FTA Data Providers
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //first time application dataproviders
            'ftaHosDataProvider' => $ftaHosDataProvider,
            'ftaHodDataProvider' => $ftaHodDataProvider,
            'approvedHodDataProvider' => $approvedHodDataProvider,
            'ftaRegistryDataProvider' => $ftaRegistryDataProvider,
            //'ftaHodDisapprovedDataProvider' => $ftaHodDisapprovedDataProvider,
            // Extension Data Providers
            'extensionHosDataProvider' => $extensionHosDataProvider,
            'extensionHodDataProvider' => $extensionHodDataProvider,
            'extensionRegistryDataProvider' => $extensionRegistryDataProvider,

        ]);
    }


    public function actionHosApprove()
    {
        return $this->approveLeave(Yii::$app->request->post('applicationId'), 'HOS');
    }

    public function actionHodApprove()
    {
        return $this->approveLeave(Yii::$app->request->post('applicationId'), 'HOD');
    }

    public function actionRegistryApprove()
    {
        return $this->approveLeave(Yii::$app->request->post('applicationId'), 'REGISTRY');
    }
    /**
     * Centralized leave approval method.
     * 
     * @param integer $applicationId The ID of the leave application
     * @param integer $levelId The current approval level
     * @return Response
     */

    protected function approveLeave($applicationId, $levelName)
    {
        $levelId = $this->getLevelId($levelName);
        if ($levelId === null) {
            Yii::$app->session->addFlash('error', 'Invalid approval level: ' . $levelName);
            return $this->redirect(['index']);
        }

        Yii::info("approveLeave called with applicationId: {$applicationId}, levelId: {$levelId}", __METHOD__);

        $leaveApproval = LeaveApproval::findOne([
            'application_id' => $applicationId,
            'level_id' => $levelId
        ]);

        if ($leaveApproval) {
            // Start transaction
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $applicationId = Yii::$app->request->post('applicationId');
                $statusName = Yii::$app->request->post('status');
                $remark = Yii::$app->request->post('remark');
                $daysApplied = (int)Yii::$app->request->post('days_applied');
                $extensionDays = (int)Yii::$app->request->post('extension_days');


                Yii::info("StatusName: {$statusName}, Remark: {$remark}, DaysApplied: {$daysApplied}", __METHOD__);

                $leaveApplication = LeaveApplication::findOne($applicationId);
                $staffDetail = StaffDetail::findOne($leaveApplication->payroll_no);

                $applicantName = $staffDetail ? $staffDetail->surname : 'Applicant';
                $applicantEmail = $staffDetail ? $staffDetail->email : null;

                if (!empty($remark)) {
                    $statusId = $this->getStatusId($statusName);
                    if ($statusId === null) {
                        Yii::$app->session->addFlash('error', 'Invalid status: ' . $statusName);
                        return $this->redirect(['index']);
                    }


                    switch ($levelName) {
                        case 'HOS':
                            if ($leaveApproval->extend) { // Check in LeaveApproval if it's an extension
                                // Handle extension for HOS
                                if ($statusName === 'FORWARDED') {
                                    $leaveApproval->status_id = $this->getStatusId('FORWARDED');
                                    $leaveApproval->level_id = $this->getLevelId('HOD');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'HOS FORWARDED (Extension)');
                                } elseif ($statusName === 'DISAPPROVED') {
                                    $leaveApproval->status_id = $this->getStatusId('DISAPPROVED');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'HOS DISAPPROVED (Extension)');
                                } else {
                                    Yii::$app->session->addFlash('error', 'Invalid action for HOS extension.');
                                    return $this->redirect(['index']);
                                }
                            } else {
                                // Original leave application logic
                                if ($statusName === 'FORWARDED') {
                                    $leaveApproval->status_id = $this->getStatusId('FORWARDED');
                                    $leaveApproval->level_id = $this->getLevelId('HOD');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'HOS FORWARDED');
                                } elseif ($statusName === 'DISAPPROVED') {
                                    $leaveApproval->status_id = $this->getStatusId('DISAPPROVED');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'HOS DISAPPROVED');
                                } elseif ($statusName === 'REVIEW') {
                                    $leaveApproval->status_id = $this->getStatusId('REVIEW');
                                    $leaveApproval->level_id = $this->getLevelId('APPLICANT');
                                    $leaveApproval->remark = $this->formatRemark($remark, '(HOS) REVIEW APPLICATION');
                                } else {
                                    Yii::$app->session->addFlash('error', 'Invalid action for HOS.');
                                    return $this->redirect(['index']);
                                }
                            }
                            break;

                        case 'HOD':
                            if ($leaveApproval->extend) { // Check in LeaveApproval if it's an extension
                                // Handle extension for HOD
                                if ($statusName === 'APPROVED') {
                                    $leaveApproval->status_id = $this->getStatusId('APPROVED');
                                    $leaveApproval->level_id = $this->getLevelId('REGISTRY');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'HOD APPROVED (EXTENSION)');

                                    // Deduct the extended leave days using LeaveExtension->extension_days
                                    $leaveExtension = LeaveExtension::findOne(['application_id' => $applicationId]);
                                    if ($leaveExtension) {
                                        $leaveTransaction = new LeaveTransactions();
                                        $leaveTransaction->payroll_no = $staffDetail->payroll_no;
                                        $leaveTransaction->days = - ($leaveExtension->extension_days); // Deduct extension days
                                        $leaveTransaction->year = date('Y');
                                        $leaveTransaction->reference_no = $leaveApplication->application_id;
                                        $leaveTransaction->transaction_name = 'LEAVE EXTENSION APPROVED BY HOD';
                                        if (!$leaveTransaction->save()) {
                                            throw new \Exception('Failed to save LeaveTransaction: ' . json_encode($leaveTransaction->getErrors()));
                                        }
                                    } else {
                                        throw new \Exception('Leave extension data not found.');
                                    }
                                } elseif ($statusName === 'DISAPPROVED') {
                                    $leaveApproval->status_id = $this->getStatusId('DISAPPROVED');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'HOD DISAPPROVED (EXTENSION)');
                                }
                            } else {
                                // Original leave application logic
                                if ($statusName === 'APPROVED') {
                                    $leaveApproval->status_id = $this->getStatusId('APPROVED');
                                    $leaveApproval->level_id = $this->getLevelId('REGISTRY');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'HOD APPROVED');

                                    // Original leave deduction logic
                                    $leaveTransaction = new LeaveTransactions();
                                    $leaveTransaction->payroll_no = $staffDetail->payroll_no;
                                    $leaveTransaction->days = -$leaveApplication->days_applied;
                                    $leaveTransaction->year = date('Y');
                                    $leaveTransaction->reference_no = $leaveApplication->application_id;
                                    $leaveTransaction->transaction_name = 'FINAL LEAVE APPROVAL BY HOD';
                                    if (!$leaveTransaction->save()) {
                                        throw new \Exception('Failed to save LeaveTransaction: ' . json_encode($leaveTransaction->getErrors()));
                                    }
                                } elseif ($statusName === 'DISAPPROVED') {
                                    $leaveApproval->level_id = $this->getLevelId('APPLICANT');
                                    $leaveApproval->status_id = $this->getStatusId('DISAPPROVED');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'HOD DISAPPROVED');
                                }
                            }
                            break;
                            case 'REGISTRY':
                                if ($statusName === 'APPROVED') {
                                    Yii::$app->session->addFlash('success', 'Leave application updated successfully.');
                                } elseif ($statusName === 'VERIFIED') {
                                    // Check if leaveApproval->extend is true or false
                                    if ($leaveApproval->extend) {
                                        // Handle Extension case (extend == true)
                                        $leaveApproval->status_id = $this->getStatusId('VERIFIED');
                                        $leaveApproval->remark = $this->formatRemark($remark, 'REGISTRY VERIFIED');
                            
                                        // Retrieve extension_days from form
                                        $extensionDays = (int)Yii::$app->request->post('extension_days');
                            
                                        // Find or create LeaveExtension model
                                        $leaveExtension = LeaveExtension::findOne(['application_id' => $applicationId]); 
                                            //?: new LeaveExtension(['application_id' => $leaveApplication->application_id, 'extension_days' => 0]);
                            
                                        // Calculate the difference in extension days
                                        $oldExtensionDays = $leaveExtension->extension_days; 
                                        $daysDifference = $extensionDays - $oldExtensionDays;
                            
                                        // Determine the last extension end date
                                        $lastEndDate = $leaveExtension->extension_end_date ?? $leaveApplication->end_date;
                                        $newExtensionEndDate = $this->calculateNewExtensionEndDate($lastEndDate, $extensionDays);
                            
                                        // Update the LeaveExtension record with new values
                                        $leaveExtension->extension_end_date = $newExtensionEndDate;
                                        $leaveExtension->extension_days = $extensionDays;
                            
                                        // Save the leave extension details
                                        if ($leaveExtension->save()) {
                                            // Insert leave transaction for the difference in extension days only if it's not zero
                                            if ($daysDifference !== 0) {
                                                $leaveTransaction = new LeaveTransactions();
                                                $leaveTransaction->payroll_no = $staffDetail->payroll_no;
                                                $leaveTransaction->days = -$daysDifference; 
                                                $leaveTransaction->year = date('Y');
                                                $leaveTransaction->reference_no = $leaveApplication->application_id;
                                                $leaveTransaction->transaction_name = 'LEAVE EXTENSION ADJUSTMENT BY REGISTRY';
                            
                                                if ($leaveTransaction->save()) {
                                                    // Send notification emails for the extension
                                                    $this->sendEmailNotification(
                                                        $applicantEmail,
                                                        $applicantName,
                                                        $this->getStatusId('VERIFIED'),
                                                        $remark,
                                                        $levelName,
                                                        $leaveApplication,
                                                        $leaveExtension->extension_days
                                                    );
                            
                                                    Yii::info("Extension case processed and notification emails sent.", __METHOD__);
                                                } else {
                                                    Yii::error('Failed to save LeaveTransaction: ' . json_encode($leaveTransaction->getErrors()), __METHOD__);
                                                    Yii::$app->session->addFlash('error', 'Failed to update leave transactions for extension.');
                                                }
                                            } else {
                                                Yii::info("No change in extension days; no transaction recorded.", __METHOD__);
                                            }
                                        } else {
                                            // Handle save errors for LeaveExtension
                                            $errors = implode(', ', array_map(function ($error) {
                                                return implode(', ', $error);
                                            }, $leaveExtension->getErrors()));
                                            Yii::error('Failed to update LeaveExtension: ' . json_encode($leaveExtension->getErrors()), __METHOD__);
                                            Yii::$app->session->addFlash('error', 'Failed to update leave extension: ' . $errors);
                                        }
                                    } elseif (!$leaveApproval->extend) { // Check if leaveApproval->extend is false for FTA
                                        // Handle First-Time Application (FTA) case
                                        if ($statusName === 'VERIFIED') { // Check if statusName is VERIFIED first
                                            $leaveApproval->status_id = $this->getStatusId('VERIFIED');
                                            $leaveApproval->remark = $this->formatRemark($remark, 'REGISTRY VERIFIED');
                            
                                            
                                            $oldDaysApplied = $leaveApplication->days_applied;
                            
                                            if ($oldDaysApplied !== $daysApplied) {
                                                // Update the leave application with the new days_applied value
                                                $leaveApplication->days_applied = $daysApplied;
                            
                                                // Calculate the new end_date excluding weekends and holidays
                                                $newEndDate = $this->calculateNewEndDate($leaveApplication->start_date, $daysApplied);
                                                $leaveApplication->end_date = $newEndDate; // Update the end_date
                            
                                                // Save the updated leave application
                                                if ($leaveApplication->save()) {
                                                    // Calculate the difference in leave days
                                                    $daysDifference = $daysApplied - $oldDaysApplied;
                            
                                                    if ($daysDifference !== 0) {
                                                        // Create a leave transaction for the difference in days
                                                        $leaveTransaction = new LeaveTransactions();
                                                        $leaveTransaction->payroll_no = $staffDetail->payroll_no;
                                                        $leaveTransaction->days = -$daysDifference;
                                                        $leaveTransaction->year = date('Y');
                                                        $leaveTransaction->reference_no = $leaveApplication->application_id;
                                                        $leaveTransaction->transaction_name = 'LEAVE DAYS ADJUSTMENT BY REGISTRY';
                            
                                                        if (!$leaveTransaction->save()) {
                                                            Yii::error('Failed to save LeaveTransaction: ' . json_encode($leaveTransaction->getErrors()), __METHOD__);
                                                            Yii::$app->session->addFlash('error', 'Failed to update leave transactions.');
                                                        }
                                                    }
                            
                                                    // Send notification emails
                                                    $this->sendEmailNotification(
                                                        $applicantEmail,
                                                        $applicantName,
                                                        $this->getStatusId('VERIFIED'),
                                                        $remark,
                                                        $levelName,
                                                        $leaveApplication,
                                                        $oldDaysApplied,
                                                        $daysApplied
                                                    );
                            
                                                    Yii::info("Days change and leave verification emails sent on VERIFY with change.", __METHOD__);
                                                } else {
                                                    // Handle save errors for LeaveApplication
                                                    $errors = implode(', ', array_map(function ($error) {
                                                        return implode(', ', $error);
                                                    }, $leaveApplication->getErrors()));
                                                    Yii::error('Failed to update LeaveApplication: ' . json_encode($leaveApplication->getErrors()), __METHOD__);
                                                    Yii::$app->session->addFlash('error', 'Failed to update leave application days: ' . $errors);
                                                }
                                            } else {
                                                // No change in days_applied, just send the verification email
                                                $this->sendEmailNotification(
                                                    $applicantEmail,
                                                    $applicantName,
                                                    $this->getStatusId('VERIFIED'),
                                                    $remark,
                                                    $levelName,
                                                    $leaveApplication
                                                );
                                                Yii::info("Leave verification email sent on VERIFY with no change.", __METHOD__);
                                            }
                                        } else {
                                            Yii::$app->session->addFlash('error', 'Leave approval extension is not applicable for FTA.');
                                        }
                                    } else {
                                        Yii::$app->session->addFlash('error', 'Leave approval extension is not applicable for FTA.');
                                    }
                                } elseif ($statusName === 'DISAPPROVED') {
                                    // Handle REJECT case
                                    $leaveApproval->level_id = $this->getLevelId('HOD');
                                    $leaveApproval->remark = $this->formatRemark($remark, 'REGISTRY REJECTED');
                                } else {
                                    Yii::$app->session->addFlash('error', 'Invalid action for REGISTRY.');
                                    return $this->redirect(['index']);
                                }
                                break;
                            
                        default:
                            Yii::$app->session->addFlash('error', 'Invalid approval level.');
                            return $this->redirect(['index']);
                    }
                    $timezone = new \DateTimeZone('Africa/Nairobi');  // Adjust to your time zone
                    $leaveApproval->approval_date = (new \DateTime('now', $timezone))->format('Y-m-d H:i:s');

                    //$leaveApproval->approval_date = (new \DateTime())->format('Y-m-d H:i:s');

                    if (!$leaveApproval->save()) {
                        throw new \Exception('Failed to update LeaveApproval: ' . json_encode($leaveApproval->getErrors()));
                    }

                    Yii::info("Leave application updated successfully. StatusName: {$statusName}", __METHOD__);
                    Yii::$app->session->addFlash('success', 'Leave application updated successfully.');

                    // Check if the status is not APPROVED before saving the approval history
                    if (!($levelName === 'REGISTRY' && $statusName === 'APPROVED')) {
                        $this->saveApprovalHistory($applicationId, $levelId, $this->getStatusId($statusName), $remark, $levelName);

                        if ($applicantEmail) {
                            $this->sendEmailNotification($applicantEmail, $applicantName, $this->getStatusId($statusName), $remark, $levelName, $leaveApplication);
                        } else {
                            Yii::$app->session->addFlash('error', 'Applicant email not found.');
                        }
                    }

                    // Commit the transaction
                    $transaction->commit();
                } else {
                    throw new \Exception('Remark is empty.');
                }
            } catch (\Exception $e) {
                // Rollback the transaction
                $transaction->rollBack();
                Yii::error('Transaction rolled back. Error: ' . $e->getMessage(), __METHOD__);
                Yii::$app->session->addFlash('error', 'An error occurred while processing the leave application: ' . $e->getMessage());
            }

            return $this->redirect(['index']);
        }
    }

    protected function getLevelId($levelName)
    {
        // Convert the input to uppercase before searching
        $level = ApprovalLevel::findOne(['level_name' => strtoupper($levelName)]);
        return $level ? $level->level_id : null; // Return null if not found
    }

    protected function getStatusId($statusName)
    {
        // Convert the input to uppercase before searching
        $status = ApprovalStatus::findOne(['status_name' => strtoupper($statusName)]);
        return $status ? $status->status_id : null; // Return null if not found
    }
    // Function to get the status label based on the status ID
    protected function getStatusLabel($statusId)
    {
        $status = ApprovalStatus::find()->where(['status_id' => $statusId])->one();
        return $status ? $status->status_name : 'Unknown Status';
    }
    // Function to send email for leave adjustment
    protected function sendEmailNotification($applicantEmail, $applicantName, $statusId, $remark, $approverRole, $leaveApplication, $oldDaysApplied = null, $newDaysApplied = null)
    {
        $mail = new PHPMailer(true);
        // Get the status label from the status ID
        $statusLabel = $this->getStatusLabel($statusId);
        try {
            // Determine if we need to send the days adjustment email
            if ($oldDaysApplied !== null && $newDaysApplied !== null && $oldDaysApplied != $newDaysApplied) {
                $daysAdded = $newDaysApplied - $oldDaysApplied;
                // Send days adjustment notification email

                $subject = 'Leave Days Adjustment Notification';
                $content = Yii::$app->view->render('@app/mail/views/daysChangeNotification', [
                    'applicantName' => $applicantName,
                    'newDaysApplied' => $newDaysApplied,
                    'oldDaysApplied' => $oldDaysApplied,
                    'daysAdded' => $daysAdded,
                    'leaveApplication' => $leaveApplication,
                ]);
            } else {
                // Prepare normal leave verification or status update email based on the statusId
                switch ($statusId) {
                    case $this->getStatusId('FORWARDED'):
                        $subject = 'Your Leave Application has been Forwarded';
                        $content = Yii::$app->view->render('@app/mail/views/leaveForwarded', [
                            'applicantName' => $applicantName,
                            'remark' => $remark,
                            'status' => $statusLabel,
                            'approverRole' => 'HOD',
                        ]);
                        break;
                    case $this->getStatusId('REVIEW'):
                        $subject = 'Your Leave Application Requires Review';
                        $content = Yii::$app->view->render('@app/mail/views/leaveReview', [
                            'applicantName' => $applicantName,
                            'remark' => $remark,
                            'status' => $statusLabel,
                            'approverRole' => $approverRole,
                        ]);
                        break;
                    case $this->getStatusId('APPROVED'): // Approved by HOD
                        $subject = 'Your Leave Application has been Approved';
                        $content = Yii::$app->view->render('@app/mail/views/leaveApproval', [
                            'applicantName' => $applicantName,
                            'remark' => $remark,
                            'status' => $statusLabel,
                            'approverRole' => $approverRole,
                            'leaveDays' => $leaveApplication ? $leaveApplication->days_applied : null,
                            'startDate' => $leaveApplication ? $leaveApplication->start_date : null,
                            'endDate' => $leaveApplication ? $leaveApplication->end_date : null,

                        ]);

                        break;

                    case $this->getStatusId('VERIFIED'):
                        $subject = 'Your Leave Application has been Verified';
                        $content = Yii::$app->view->render('@app/mail/views/leaveVerification', [
                            'applicantName' => $applicantName,
                            'status' => $statusLabel,
                            'remark' => $remark,
                            'approverRole' => $approverRole,
                            'leaveDays' => $leaveApplication ? $leaveApplication->days_applied : null,
                            'startDate' => $leaveApplication ? $leaveApplication->start_date : null,
                            'endDate' => $leaveApplication ? $leaveApplication->end_date : null,
                        ]);
                        break;

                    case $this->getStatusId('DISAPPROVED'):
                        $subject = 'Your Leave Application has been Rejected';
                        $content = Yii::$app->view->render('@app/mail/views/leaveRejection', [
                            'applicantName' => $applicantName,
                            'remark' => $remark,
                            'status' => $statusLabel,
                            'approverRole' => $approverRole,
                        ]);
                        break;

                    default:
                        throw new Exception('Invalid status for email notification.');
                }
            }

            // Use Yii2 layout for email
            $layout = Yii::$app->view->render('@app/mail/layouts/html', [
                'content' => $content,
                'title' => $subject,
            ]);

            // PHPMailer setup
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'leave_test@uonbi.ac.ke';
            $mail->Password = 'wetu ocgy clxq iits';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('leave_test@uonbi.ac.ke', 'OLAPIS');
            $mail->addAddress('rodgitau66@gmail.com', $applicantName);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $layout;

            // Send email
            $mail->send();
            Yii::info("Email sent successfully to {$applicantEmail}", __METHOD__);
        } catch (Exception $e) {
            Yii::$app->session->addFlash('error', 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        }
    }


    protected function formatRemark($remark, $approver)
    {
        return $approver . ': ' . $remark;
    }
    public function calculateNewEndDate($startDate, $daysApplied)
    {
        $endDate = new \DateTime($startDate);
        $holidays = LeaveHolidays::find()->select('holiday_date')->column(); // Fetch holiday dates

        $daysCounted = 0;

        while ($daysCounted < $daysApplied) {
            // Move to the next day
            $endDate->modify('+1 day');

            // Check if the day is a weekend
            if ($endDate->format('N') >= 6) { // N = 6 is Saturday, N = 7 is Sunday
                continue; // Skip weekends
            }

            // Check if the day is a holiday
            if (in_array($endDate->format('Y-m-d'), $holidays)) {
                continue; // Skip holidays
            }

            // Increment valid days
            $daysCounted++;
        }

        return $endDate->format('Y-m-d');
    }
    public function calculateNewExtensionEndDate($lastExtensionEndDate, $daysApplied)
    {
        $endDate = new \DateTime($lastExtensionEndDate);
        $holidays = LeaveHolidays::find()->select('holiday_date')->column(); // Fetch holiday dates

        $daysCounted = 0;

        while ($daysCounted < $daysApplied) {
            // Move to the next day
            $endDate->modify('+1 day');

            // Check if the day is a weekend
            if ($endDate->format('N') >= 6) { // N = 6 is Saturday, N = 7 is Sunday
                continue; // Skip weekends
            }

            // Check if the day is a holiday
            if (in_array($endDate->format('Y-m-d'), $holidays)) {
                continue; // Skip holidays
            }

            // Increment valid days
            $daysCounted++;
        }

        return $endDate->format('Y-m-d');
    }

    protected function saveApprovalHistory($applicationId, $levelId, $statusId, $remark, $approverRole)
    {
        // Check if the user is logged in
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->addFlash('error', 'You need to be logged in to perform this action.');
            // Optionally, redirect to the login page
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $approvalHistory = new ApprovalHistory();
        $approvalHistory->application_id = $applicationId;
        $approvalHistory->level_id = $levelId;
        $approvalHistory->status_id = $statusId;
        $approvalHistory->remark = $remark;
        $approvalHistory->approver = Yii::$app->user->identity->username;
        $timezone = new \DateTimeZone('Africa/Nairobi');  // Replace with your time zone
        $approvalHistory->approval_date = (new \DateTime('now', $timezone))->format('Y-m-d H:i:s');


        if (!$approvalHistory->save()) {
            Yii::$app->session->addFlash('error', 'Failed to save approval history: ' . json_encode($approvalHistory->getErrors()));
        }
    }
    protected function sendEmailToCurrentUser($approvalStatus, $remark, $leaveApproval)
    {
        // Get the logged-in user's payroll_no (username) and fetch their email from StaffDetail
        $payrollNo = Yii::$app->user->identity->username;  // Assuming payroll_no is the username
        $staffDetail = StaffDetail::findOne(['payroll_no' => $payrollNo]);

        if ($staffDetail && !empty($staffDetail->email)) {
            // Prepare email content
            $subject = "Leave Application - Status: $approvalStatus";
            $content = Yii::$app->view->render('@app/mail/views/notifyApprover', [
                'approverName' => $staffDetail->surname,
                'approvalStatus' => $approvalStatus,
                'remark' => $remark,
                'applicationId' => $leaveApproval->application_id,  // Assuming leaveApproval has this property
            ]);

            // Use Yii2 layout for email content
            $layout = Yii::$app->view->render('@app/mail/layouts/html', [
                'content' => $content,
                'title' => $subject,
            ]);

            // Send the email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'leave_test@uonbi.ac.ke';
                $mail->Password = 'wetu ocgy clxq iits';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('leave_test@uonbi.ac.ke', 'OLAPIS');
                $mail->addAddress($staffDetail->email, $staffDetail->surname);  // Add recipient

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body    = $layout;

                // Send email
                $mail->send();
                Yii::$app->session->addFlash('success', 'Email notification sent to ' . $staffDetail->email);
            } catch (Exception $e) {
                Yii::error('Mail Error: ' . $mail->ErrorInfo, __METHOD__);
                Yii::$app->session->addFlash('error', 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            }
        } else {
            Yii::$app->session->addFlash('error', 'Could not retrieve email for the logged-in user.');
        }
    }
    protected function sendEmailToHODandHOS($leaveApproval)
    {
        // Fetch HOD and HOS emails from StaffDetail based on their payroll numbers
        $hodPayrollNo = Yii::$app->user->identity->username;
        $hosPayrollNo = Yii::$app->user->identity->username;

        // Get email for HOD
        $hodDetail = StaffDetail::findOne(['payroll_no' => $hodPayrollNo]);
        $hodEmail = $hodDetail ? $hodDetail->email : null;

        // Get email for HOS
        $hosDetail = StaffDetail::findOne(['payroll_no' => $hosPayrollNo]);
        $hosEmail = $hosDetail ? $hosDetail->email : null;

        $emails = array_filter([$hodEmail, $hosEmail]); // Filter out any null emails

        foreach ($emails as $email) {
            $this->sendEmailToCurrentUser('VERIFIED', "Leave application has been verified by Registry.", $leaveApproval, $email);
        }
    }
}