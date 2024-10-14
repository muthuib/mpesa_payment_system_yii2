<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
'css/site.css',
        'https://fonts.gstatic.com',
 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i',
'vendor/bootstrap/css/bootstrap.min.css',
'vendor/bootstrap-icons/bootstrap-icons.css',
'vendor/boxicons/css/boxicons.min.css',
'vendor/quill/quill.snow.css',
'vendor/quill/quill.bubble.css',
'vendor/remixicon/remixicon.css',
'vendor/simple-datatables/style.css',
        'css/style.css',
        
    ];
    public $js = [
        'vendor/apexcharts/apexcharts.min.js',
  'vendor/bootstrap/js/bootstrap.bundle.min.js',
  'vendor/chart.js/chart.umd.js',
  'vendor/echarts/echarts.min.js',
  'vendor/quill/quill.min.js',
  'vendor/simple-datatables/simple-datatables.js',
  'vendor/tinymce/tinymce.min.js',
  'vendor/php-email-form/validate.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
