<?php
namespace app\models;

use Faker\UniqueGenerator;

/**
 * Fake data generator for Comment
 * @method static Comment makeOne($attributes = [], ?UniqueGenerator $uniqueFaker = null);
 * @method static Comment saveOne($attributes = [], ?UniqueGenerator $uniqueFaker = null);
 * @method static Comment[] make(int $number, $commonAttributes = [], ?UniqueGenerator $uniqueFaker = null)
 * @method static Comment[] save(int $number, $commonAttributes = [], ?UniqueGenerator $uniqueFaker = null)
 */
class CommentFaker extends BaseModelFaker
{

    /**
     * @param array|callable $attributes
     * @return Comment|\yii\db\ActiveRecord
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
        $model = new Comment();
        //$model->id = $uniqueFaker->numberBetween(0, 2147483647);
        $model->message = $faker->sentence;
        $model->meta_data = substr($faker->text(300), 0, 300);
        $model->created_at = $faker->dateTimeThisYear('now', 'UTC')->format(DATE_ATOM);
        if (!is_callable($attributes)) {
            $model->setAttributes($attributes, false);
        } else {
            $model = $attributes($model, $faker, $uniqueFaker);
        }
        return $model;
    }
}
