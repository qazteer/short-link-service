<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "links".
 *
 * @property int $id
 * @property string $long_url
 * @property string $short_code
 * @property int|null $limit
 * @property int|null $hits
 * @property string $lifetime
 * @property string $created_dt
 * @property string|null $updated_dt
 */
class Links extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'links';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['long_url', 'lifetime', 'limit'], 'required'],
            [['limit', 'hits'], 'integer'],
            [['lifetime', 'created_dt', 'updated_dt'], 'safe'],
            [['short_code'], 'string', 'max' => 255],
            [['lifetime'], 'integer', 'min' => 1, 'max' => 24],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'long_url' => 'Long Url',
            'short_code' => 'Short Code',
            'limit' => 'Limit',
            'hits' => 'Hits',
            'lifetime' => 'Lifetime',
            'created_dt' => 'Created Dt',
            'updated_dt' => 'Updated Dt',
        ];
    }
}
