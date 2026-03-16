<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "app_monitor".
 *
 * @property int $id
 * @property string $alias
 * @property int $priority
 * @property int $created_at
 * @property int $updated_at
 */
class AppMonitor extends \yii\db\ActiveRecord
{
    const PRIORITY_HIGH = 100;
    const PRIORITY_MEDIUM = 50;
    const PRIORITY_LOW = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'app_monitor';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alias', 'priority', 'created_at', 'updated_at'], 'required'],
            [['priority', 'created_at', 'updated_at'], 'integer'],
            [['alias'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias' => 'Alias',
            'priority' => 'Priority',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function updateStatus($alias){
        $appMonitor = self::findOne(['alias' => $alias]);
        if ($appMonitor) {
            $appMonitor->updated_at = time();
            return $appMonitor->save();
        }
        return false;
    }
    

    /**
     * Checks the application status for user level access.
     * Returns true if all high priority services are valid, false otherwise.
     * 
     * @return bool
     */
    public static function getAppStatusUserLevel() {
        $servicesConfiguration = Yii::$app->params['app_monitor']['services'] ?? [];
        
        $highPriorityRecords = static::find()
            ->where(['priority' => self::PRIORITY_HIGH])
            ->all();

        foreach ($highPriorityRecords as $record) {
            $alias = $record->alias;
            $validForMinutes = $servicesConfiguration[$alias]['valid_for'] ?? 0;
            $validThreshold = time() - ($validForMinutes * 60);

            if ($record->updated_at < $validThreshold) {
                return false;
            }
        }

        return true;
    }


    /**
     * Checks the application status for admin level access.
     * Returns true if all services are valid, false otherwise.
     * 
     * @return bool
     */
    public static function getAppStatusAdminLevel()
    { 
        $servicesConfiguration = Yii::$app->params['app_monitor']['services'] ?? [];

        $records = static::find()->all();

        foreach ($records as $record) {

            // Nepoužívat kontrolu na DELETE_OLD_LOGS
            if (strtoupper($record->alias) === 'DELETE_OLD_LOGS') {
                continue;
            }

            $alias = $record->alias;
            $validForMinutes = $servicesConfiguration[$alias]['valid_for'] ?? 0;
            $validThreshold = time() - ($validForMinutes * 60);

            if ($record->updated_at < $validThreshold) {
                return false;
            }
        }

        return true;
    }
}
