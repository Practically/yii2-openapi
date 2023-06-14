<?php
/**
 * Template used by Gii to generate custom controller PHP classes.
 *
 * These are the controllers that don't get overwritten on subsequent
 * generations, for custom logic to be included by the developer.
 *
 * @var yii\web\View $this
 * @var string $className
 * @var string $namespace
 * @var cebe\yii2openapi\lib\items\RestAction[] $actions
 * @var cebe\yii2openapi\generator\ApiGenerator $generator
 */

use cebe\yii2openapi\lib\items\RestAction;

$abstractActions = array_filter($actions, static function (RestAction $action) {
    return $action->shouldBeAbstract();
});

echo '<?php';

?>


namespace <?= $namespace ?>;

/**
 * Extended <?= $className ?> class.
 *
 * This file won't be overwritten by subsequent code generation tasks. Put your
 * custom actions and logic here.
 */
class <?= $className ?> extends <?= "$namespace\\base\\$className" ?>

{

<?php if ($generator->useJsonApi) : ?>
    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

<?php endif; ?>
    public function checkAccess($action, $model = null, $params = [])
    {
        //TODO implement checkAccess
    }

<?php foreach ($abstractActions as $action) : ?>
<?php $params = array_map(
    static function ($param) {
        return ['name' => $param];
    },
    $action->getParamNames()
) ?>
    public function <?= $action->actionMethodName ?>(<?= implode(', ', $params) ?>)
    {
        //TODO implement <?= $action->actionMethodName ?>
    }

<?php endforeach; ?>

}
