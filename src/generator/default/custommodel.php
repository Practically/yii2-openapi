<?php
/**
 * Template used by Gii to generate custom model PHP classes.
 *
 * These are the models that don't get overwritten on subsequent generations,
 * for custom logic to be included by the developer.
 *
 * @var yii\web\View $this
 * @var cebe\yii2openapi\lib\items\DbModel $model
 * @var string $namespace
 * @var string $extends
 * @var cebe\yii2openapi\generator\ApiGenerator $generator
 */

echo '<?php';

?>

namespace <?= $namespace ?>;

/**
 *<?= empty($model->description) ? '' : str_replace("\n", "\n * ", ' '.trim($model->description)) ?>

 * This file won't be overwritten by subsequent code generation tasks.
 */
class <?= $model->getClassName() ?> extends \<?= $extends ?>

{
}
