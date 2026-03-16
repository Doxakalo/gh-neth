<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserBet;
use common\models\Odd;
use common\models\Transaction;
use common\utils\ModelUtils;

/**
 * Bet form
 */
class BetForm extends Model
{
    public $amount;
    public $odd_id;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount'], 'required', 'message' => Yii::t('app', 'bet_form_amount_required')],
            [['amount'], 'number', 'min' => 1,
                'tooSmall' => Yii::t('app', 'bet_form_amount_minimum', ['min' => Yii::$app->params['bet.minimumAmount']]),
                'message' => Yii::t('app', 'bet_form_amount_numeric'),
            ],
            [['amount'], 'validateUserFunds'],
            
            [['odd_id'], 'required'],
            [['odd_id'], 'integer'],
            [['odd_id'], 'exist', 'targetClass' => Odd::class, 'targetAttribute' => 'id'],
            [['odd_id'], 'validateMatchAvailable'],
        ];
    }


    /**
     * Validates the user's funds to ensure they have enough balance to place the bet.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUserFunds($attribute, $params) {
        $user = $this->getUser();
        if ($user->getFundBalance() < $this->amount) {
            $this->addError($attribute, Yii::t('app', 'bet_form_amount_insufficient_funds'));
        }
    }


    /**
     * Validates if the match is not in progress or inactive.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateMatchAvailable($attribute, $params) {
        $odd = Odd::findOne($this->odd_id);
        $match = $odd->sportMatch;

        if ($match->isInProgress()) {
            $this->addError($attribute, Yii::t('app', 'bet_form_match_in_progress'));
        }

        if (!$match->isActive()) {
            $this->addError($attribute, Yii::t('app', 'bet_form_match_inactive'));
        }
    }


    /**
     * Save bet and create fund transaction.
     * 
     * @return bool whether the bet was successfully placed
     */
    public function submit() {
		if (!$this->validate()) {
			return false;
		}

		$transaction = \Yii::$app->db->beginTransaction();
		try {
			$bet = $this->createBet();
			if (!$bet) {
				$this->addError('general', 'Error while creating bet.');
				$transaction->rollBack();
				return false;
			}

			if(!$this->updateFunds($bet)) {
				$this->addError('general', 'Error while updating funds.');
				$transaction->rollBack();
				return false;
			}

			$transaction->commit();
			return true;

		} catch (\Exception $e) {
			$this->addError('general', 'An error occurred during bet placement.');
			$this->addError('general', $e->getMessage());

			$transaction->rollBack();
			return false;
		}
	}


	/**
	 * Create bet
	 *
	 * @return UserBet|bool bet model if successfull or false if saving fails
	 */
	private function createBet() {
        $odd = Odd::findOne($this->odd_id);
        $bet = new UserBet();
        $bet->user_id = $this->getUser()->id;
        $bet->amount = $this->amount;
        $bet->odd_id = $odd->id;
        $bet->odd = $odd->odd; // copy odd value to bet's odd value

		if ($bet->validate() && $bet->save()) {
			return $bet;
		}

		ModelUtils::addModelErrors($this, $bet);
		return false;
	}


	/**
	 * Update funds
	 *
	 * @return bool
	 */
	private function updateFunds(UserBet $bet) {
		$fundsTransaction = new Transaction();
		$fundsTransaction->user_id = $this->getUser()->id;
		$fundsTransaction->type = Transaction::TYPE_BET;
		$fundsTransaction->amount = ($bet->amount) * -1; // negative amount for bet
        $fundsTransaction->user_bet_id = $bet->id; 
		$fundsTransaction->setActionLabel();
		$fundsTransaction->setDescriptionLabel();

		if ($fundsTransaction->validate() && $fundsTransaction->save()) {
			return true;
		}

		ModelUtils::addModelErrors($this, $fundsTransaction);
		return false;
	}


    /**
     * Get current user
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::getCurrent();
        }

        return $this->_user;
    }

	
}
