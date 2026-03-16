<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Rbac;
use common\models\SportMatch;
use common\models\SportMatchSearch;
use common\utils\formatters\SbcFormatter;
use backend\models\SportMatchResultForm;
use common\models\Category;
use common\models\Season;
use common\models\CategorySearch;
use common\models\AllowedCategory;

class CategoriesController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
				'access' => [
					'class' => \yii\filters\AccessControl::class,
					'rules' => [
						[
							'allow' => true,
							'roles' => [Rbac::PERM_MATCH_VIEW],
							'actions' => ['index', 'view'],
						],							
						[
							'allow' => true,
							'roles' => [Rbac::PERM_MATCH_UPDATE],
							'actions' => ['enable-disable'],
						],					
					],
				],	
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }


    /**
     * @inheritDoc
     */
	public function beforeAction($action)
	{
        SbcFormatter::setDefaultUserLocale();

		if (!parent::beforeAction($action)) {
			return false;
		}
		
		return true;
	}


    /**
     * Lists all SportMatch models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = [
            'categoryName' => SORT_ASC,
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEnableDisable()
    {
        $request = Yii::$app->request;

        $id = $request->post('id');
        $vendorId = $request->post('vendor_id');
        $sportId = $request->post('sport_id');
        $sportName = $request->post('sport');
        $enabled = $request->post('enabled');


        $model = Category::findOne($id);
        if ($model) {
            $model->enabled = $enabled ? 1 : 0;
            $model->save(false);
        }

        $seasons = Season::find()->where(['category_id' => $id])->all();
        foreach ($seasons as $season) {
            $season->category_enabled = $enabled ? 1 : 0;
            $season->save(false);
        }

        if ($enabled) {
            $model = new \common\models\AllowedCategory();
            $model->id_vendor = $vendorId;
            $model->sport_id = $sportId;
            $model->sport = $sportName;
            $model->country_name = $request->post('country_name', null);
            $model->save(false);

        } else {
            $model = \common\models\AllowedCategory::find()
                ->where([
                    'id_vendor' => $vendorId,
                    'sport_id' => $sportId,
                    'sport' => $sportName,
                ])
                ->one();

            if ($model) {
                $model->delete();
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }


}