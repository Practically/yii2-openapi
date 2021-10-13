<?php
namespace app\models;

use Faker\UniqueGenerator;

/**
 * Fake data generator for Photos2Posts
 * @method static Photos2Posts makeOne($attributes = [], ?UniqueGenerator $uniqueFaker = null);
 * @method static Photos2Posts saveOne($attributes = [], ?UniqueGenerator $uniqueFaker = null);
 * @method static Photos2Posts[] make(int $number, $commonAttributes = [], ?UniqueGenerator $uniqueFaker = null)
 * @method static Photos2Posts[] save(int $number, $commonAttributes = [], ?UniqueGenerator $uniqueFaker = null)
 */
class Photos2PostsFaker extends BaseModelFaker
{

    /**
     * @param array|callable $attributes
     * @return Photos2Posts|\yii\db\ActiveRecord
     * @example
     *  $model = (new PostFaker())->generateModels(['author_id' => 1]);
     *  $model = (new PostFaker())->generateModels(function($model, $faker, $uniqueFaker) {
     *            $model->scenario = 'create';
     *            $model->author_id = 1;
     *            return $model;
     *  });
    **/
    public function generateModel($attributes = [])
    {
        $faker = $this->faker;
        $uniqueFaker = $this->uniqueFaker;
        $model = new Photos2Posts();
        //$model->id = $uniqueFaker->numberBetween(0, 2147483647);
        if (!is_callable($attributes)) {
            $model->setAttributes($attributes, false);
        } else {
            $model = $attributes($model, $faker, $uniqueFaker);
        }
        return $model;
    }
}
