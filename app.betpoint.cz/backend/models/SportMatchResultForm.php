<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\SportMatchResult;
use common\models\User;
use common\utils\ModelUtils;


/**
 * Sport Match Result form model
 */
class SportMatchResultForm extends Model
{
	public $sport_match_result_id = null;
	public $home_score = 0;
	public $away_score = 0;

	/**
	 * @var SportMatchResult|null
	 */
	private $_originalResult;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
		return [
				[['sport_match_result_id'], 'required'],
				[['sport_match_result_id'], 'integer'],
				[['sport_match_result_id'],
					'exist',
					'targetClass' => SportMatchResult::class,
					'targetAttribute' => ['sport_match_result_id' => 'id'],
					'message' => 'Sport match result does not exist.',
				],

				[['home_score', 'away_score'], 'required', 'message' => Yii::t('app', 'model_field_required')],
				[['home_score', 'away_score'], 'integer',
					'min' => 0,
					'tooSmall' => Yii::t('app', 'sport_match_result_form_score_minimum', ['min' => 0]),
				],
		];
    }


	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'home_score' => 'Home - Score',
			'away_score' => 'Away - Score',
		];
	}


	/**
	 * Load initial values from the original result
	 * 
	 * @return bool Returns true if values were loaded successfully, false otherwise
	 */
	public function loadInitialValues() {
		$originalResult = $this->getOriginalResult();
		if(!$originalResult) {
			return false;
		}
		$values = $originalResult->getResultValues();
		$this->home_score = $values['home'] ?? 0;
		$this->away_score = $values['away'] ?? 0;
		return true;
	}


	/**
	 * Submit - create new result with updated values based on the original result
	 *
	 * @return SportMatchResult|false Returns the new SportMatchResult object on success, false on failure
	 */
	public function submit() {
		if (!$this->validate()) {
			return false;
		}

		$originalResult = $this->getOriginalResult();

		$newResult = new SportMatchResult();
		$newResult->sport_match_id = $originalResult->sport_match_id;
		$newResult->user_id = $this->getAdminUserId();
		$newResult->setResultValues($this->home_score, $this->away_score);

		if ($newResult->validate() && $newResult->save()) {
			return $newResult;
		}

		ModelUtils::addModelErrors($this, $newResult);
		return false;
	}


	/**
	 * Retrieves the ID of the currently authenticated admin user.
	 *
	 * @return int|null The user ID
	 */
	private function getAdminUserId() {
		$user = User::getCurrent();
		return $user ? $user->id : null;
	}


	/**
	 * Get the original sport match result
	 *
	 * @return SportMatchResult|null
	 */
	public function getOriginalResult() {
		if ($this->_originalResult === null) {
			$this->_originalResult = SportMatchResult::findOne($this->sport_match_result_id);
		}
		return $this->_originalResult;
	}

}
