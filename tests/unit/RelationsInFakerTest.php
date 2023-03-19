<?php

namespace tests\unit;

use cebe\yii2openapi\generator\ApiGenerator;
use tests\DbTestCase;
use Yii;
use yii\db\mysql\Schema as MySqlSchema;
use yii\db\pgsql\Schema as PgSqlSchema;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\helpers\StringHelper;
use yii\validators\DateValidator;
use function array_filter;
use function getenv;
use function strpos;

class RelationsInFakerTest extends DbTestCase
{
    public function testIndex()
    {
        $testFile = Yii::getAlias("@specs/relations_in_faker/relations_in_faker.php");
        $this->runGenerator($testFile, 'mysql');

        $fakers = FileHelper::findFiles(\Yii::getAlias('@app/models'), [
            'only' => ['*Faker.php'],
            'except' => ['BaseModelFaker.php'],
        ]);

        // ----
        $modelsDependencies = [];
        foreach($fakers as $fakerFile) {
            $className = 'app\\models\\' . StringHelper::basename($fakerFile, '.php');
            $faker = new $className;

            $modelClassName = str_replace(
                'Faker',
                '',
                StringHelper::basename($fakerFile, '.php')
            );

            if (!method_exists($className, 'dependentOn')) {
                $modelsDependencies[$modelClassName] = null;
            } else {
                $modelsDependencies[$modelClassName] = $className::dependentOn();
            }
        }

        $standalone = array_filter($modelsDependencies, function ($elm) {
            return $elm === null;
        });

        $dependent = array_filter($modelsDependencies, function ($elm) {
            return $elm !== null;
        });

        $justDepenentModels = array_keys($dependent);
        $sortedDependentModels = $justDepenentModels;


        foreach ($justDepenentModels as $model) {
            if ($modelsDependencies[$model] !== null) {
                foreach ($modelsDependencies[$model] as $dependentOn) {
                    if ($modelsDependencies[$dependentOn] !== null) {
                        // move $dependentOn before $model in clone

                        // moveModel
                        // in that function if it is already before (sorted) then avoid it
                        static::moveModel($sortedDependentModels, $dependentOn, $model);
                    }
                }
            }
        }

        $finalSortedModels = array_merge(array_keys($standalone), $sortedDependentModels);
        $this->assertNull($finalSortedModels);
    }

    public static function moveModel(&$sortedDependentModels, $dependentOn, $model)
    {
        $modelKey = array_search($model, $sortedDependentModels);
        $depKey = array_search($dependentOn, $sortedDependentModels);
        if ($depKey < $modelKey) {
            return;
        }

        unset($sortedDependentModels[$depKey]);

        $restRight = array_slice($sortedDependentModels, $modelKey);
        $theKey = (($modelKey) < 0) ? 0 : ($modelKey);
        $restLeft = array_slice($sortedDependentModels, 0, $theKey);

        $sortedDependentModels = array_merge($restLeft, [$dependentOn], $restRight);
    }
}
