<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "phrase".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $order
 * @property integer $user_id
 * @property integer $deleted
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property Upload[] $uploads
 */
class Phrase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'phrase';
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();

        // Remove fields that contain sensitive information
        unset($fields['user_id']);

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'order', 'user_id'], 'required', 'on' => ['create', 'update']],
            [['description'], 'string'],
            [['order', 'user_id', 'deleted', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'order' => Yii::t('app', 'Order'),
            'user_id' => Yii::t('app', 'User ID'),
            'deleted' => Yii::t('app', 'Deleted'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUploads()
    {
        return $this->hasMany(Upload::className(), ['phrase_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->scenario == 'create') {
            $this->created_at = date('Y-m-d H:i:s');
            $this->updated_at = date('Y-m-d H:i:s');
        } elseif($this->scenario == 'update') {
            $this->updated_at = date('Y-m-d H:i:s');
        }

        return true;
    }
}
