<?php

namespace console\controllers;

use common\models\Transaction;
use yii\console\Controller;

class TransactionController extends Controller {

	/**
	 * Updates action and description labels for all transactions.
	 * 
	 * Run ./yii transaction/update-labels
	 */
	public function actionUpdateLabels() {
		$transactions = Transaction::find()->all();

		$this->stdout("Updating action and description labels for " . count($transactions) . " transactions...\n");

		foreach ($transactions as $transaction) {
			$transaction->setActionLabel();
			$transaction->setDescriptionLabel();
			if ($transaction->save()) {
				$this->stdout("ID: {$transaction->id}, Label: {$transaction->action}, Description: {$transaction->description}\n");
			}
		}

		$this->stdout("Finished updating transaction labels.\n");
	}
}
