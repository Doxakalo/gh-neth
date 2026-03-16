<?php

namespace console\controllers;

use common\models\AppMonitor;
use common\models\Category;
use common\models\Season;
use common\models\AllowedCategory;
use yii\console\Controller;
use Yii;

class CategoryController extends Controller
{
    /**
     * Enables default categories based on the configuration in params
     * and updates seasons accordingly.
     * 
     * Run ./yii category/enable-default-categories
     */
    public function actionEnableDefaultCategories()
    {
        // All categories in DB
        $categories = Category::find()->all();

        // Allowed categories from config
        $allowedCategories = AllowedCategory::find()
            ->select(['id_vendor','sport_id'])
            ->asArray()
            ->all();

        $this->stdout("Categories to enable: " . count($allowedCategories) . " of " . count($categories) . "\n");

        $enabledCategoryIds = [];

        // 1️⃣ Enable/disable categories
        foreach ($categories as $category) {
            $enableCategory = false;

            foreach ($allowedCategories as $row) {
                if ((string)$category->id_vendor === (string)$row['id_vendor']
                    && (string)$category->sport_id === (string)$row['sport_id']) {
                    $enableCategory = true;
                    break;
                }
            }

            if ($enableCategory) {
                $category->setAsEnabled();
                $enabledCategoryIds[] = $category->id;
            } else {
                $category->setAsDisabled();
            }

            // Save category and force save category_enabled
            if ($category->save(false, ['enabled'])) {
                $this->stdout("Category ID {$category->id} | Enabled: {$category->enabled}\n");
            } else {
                $this->stderr("Failed to save category ID {$category->id}\n");
            }
        }

        // 2️⃣ Update seasons for enabled categories
        if (!empty($enabledCategoryIds)) {
            $seasons = Season::find()->where(['category_id' => $enabledCategoryIds])->asArray()->all();

            $updateData = [];
            foreach ($seasons as $season) {
                $updateData[] = [
                    'id' => $season['id'],
                    'category_enabled' => 1
                ];
            }

            if (!empty($updateData)) {
                Season::batchUpdateRecordsById($updateData);
                $this->stdout("Enabled " . count($updateData) . " seasons.\n");
            }
        }

        // 3️⃣ Update monitor
        AppMonitor::updateStatus("ENABLE_DEFAULT_CATEGORIES");
        $this->stdout("Finished enabling categories and updating seasons.\n");
    }
}
