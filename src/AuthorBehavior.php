<?php
/**
 * Created by Konstantin Timoshenko
 * Date: 12/10/15
 * Time: 3:17 PM
 * Email: t.kanstantsin@gmail.com
 */

namespace tkanstantsin\yii2\behaviors\behaviors;

use yii\base\Event;
use yii\base\InvalidCallException;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;

/**
 * CreatedByBehavior automatically fills the specified attributes with the current user id.
 *
 * To use CreatedByBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use common\behaviors\CreatedByBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         CreatedByBehavior::className(),
 *     ];
 * }
 * ```
 *
 * By default, CreatedByBehavior will fill the `created_by_id` and `updated_by_id` attributes with the current user id
 * when the associated AR object is being inserted; it will fill the `updated_by_id` attribute
 * with the user id when the AR object is being updated. The user id value is obtained by `\Yii::$app->user->id`.
 *
 * If your attribute names are different or you want to use a different way of getting user id,
 * you may configure the [[createdByAttribute]], [[updatedByAttribute]] and [[value]] properties like the following:
 *
 * ```php
 * use yii\db\Expression;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => CreatedByBehavior::className(),
 *             'createdByAttribute' => 'create_time',
 *             'updatedByAttribute' => 'update_time',
 *             'value' => function ($event) {
 *                  return \Yii::$app->user->id;
 *              },
 *         ],
 *     ];
 * }
 * ```
 *
 * In case you use an [[Expression]] object as in the example above, the attribute will not hold the timestamp value, but
 * the Expression object itself after the record has been saved. If you need the value from DB afterwards you should call
 * the [[\yii\db\ActiveRecord::refresh()|refresh()]] method of the record.
 *
 * CreatedByBehavior also provides a method named [[touch()]] that allows you to assign the current
 * timestamp to the specified attribute(s) and save them to the database. For example,
 *
 * ```php
 * $model->touch('creation_time');
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Alexander Kochetov <creocoder@gmail.com>
 * @since 2.0
 */
class AuthorBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive user id value
     * Set this property to false if you do not want to record the creation author.
     */
    public $createdByAttribute = 'created_by_id';
    /**
     * @var string the attribute that will receive user id value.
     * Set this property to false if you do not want to record the update author.
     */
    public $updatedByAttribute = 'updated_by_id';
    /**
     * @var callable|Expression The expression that will be used for generating the user id.
     * This can be either an anonymous function that returns the user id,
     * or an [[Expression]] object representing a DB expression.
     * If not set, it will use the value of `Yii::$app->user->id` to set the attributes.
     */
    public $valueAuthor;

    protected $assoc;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdByAttribute, $this->updatedByAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedByAttribute,
            ];
        }
    }

    /**
     * Returns the value of the current attributes.
     * This method is called by [[evaluateAttributes()]]. Its return value will be assigned
     * to the attributes corresponding to the triggering event.
     * @param Event $event the event that triggers the current attribute updating.
     * @return mixed the attribute value
     */
    protected function getValue($event)
    {
        if ($this->value instanceof Expression) {
            return $this->value;
        } else {
            return $this->value !== null ? call_user_func($this->value, $event) : \Yii::$app->user->id;
        }
    }

    /**
     * Updates a timestamp attribute to the current timestamp.
     * If attribute is `createAtAttribute` or `updatedAtAttribute`, related user attribute will be also updated.
     *
     * ```php
     * $model->touch('lastVisit');
     * ```
     * @param string $attribute the name of the attribute to update.
     * @throws InvalidCallException if owner is a new record (since version 2.0.6).
     */
    public function touch($attribute)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Updating is not possible on a new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }
}
