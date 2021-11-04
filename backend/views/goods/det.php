<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use yii\widgets\DetailView;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="container">
        <?= Alert::widget() ?>
        <br>
        <br>
        <label class="col-sm-3 control-label" style="margin-top: 10px"><h2>基本信息</h2></label>
        <?= DetailView::widget([
            'model' => $goods,
            'attributes' => [
                'title',
                ['label'=>'编号','value'=>$goods->id],
                'status',
                ['label'=>'版本号','value'=>$goods->ver],
                ['label'=>'描述','value'=>$goods->content,'format'=>'html'],
                'memo',
//                'sort',
//                'addAt',
//                'addBy',
//                'addIP',
//                'addAgent',
            ],
        ]) ?>
        <label class="col-sm-3 control-label" style="margin-top: 10px"><h2>sku信息</h2></label>
        <?php foreach ($skuGoods as $skuGood){?>
            <?= DetailView::widget([
            'model' => $skuGood,
            'attributes' => [
                ['label'=>'编号','value'=>$skuGood['id']],
                ['label'=>'版本号','value'=>$skuGood['ver']],
                ['label'=>'库存','value'=>$skuGood['inventory']],
                ['label'=>'价格','value'=>$skuGood['price']],
                [
                        'label' => '颜色',
                        'value' => $skuGood['color'],
                ],
                [
                    'label' => 'sku图片',
                    'attribute'=>'pic',
                    'format' => ['html'],
                    'value'=>'<img src =' . $skuGood['images'] . ' height="100" width="100"' .   '>',
                ],
                //['label'=>'描述','value'=>$skuGood['content'],'format'=>'html'],
//                'sort',
//                'addAt',
//                'addBy',
//                'addIP',
//                'addAgent',
            ],
            ]) ?>
        <?php }?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
