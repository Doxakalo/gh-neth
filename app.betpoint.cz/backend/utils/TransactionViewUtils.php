<?php

namespace backend\utils;

use Yii;
use common\models\Transaction;

class TransactionViewUtils {

	/**
	 * Returns the CSS class name for the transaction type.
	 *
	 * @param int $type The type of the transaction.
	 * @return string The CSS class name corresponding to the transaction type.
	 */
	public static function getTransactionTypeClassName($type) {
		$map = [
			Transaction::TYPE_INITIAL_CREDIT => 'type-initial-credit',
			Transaction::TYPE_UPDATE_CREDIT => 'type-update-credit',
			Transaction::TYPE_BET => 'type-bet',
			Transaction::TYPE_WIN => 'type-win',
			Transaction::TYPE_REEVALUATION => 'type-reevaluation',
			Transaction::TYPE_RETURN => 'type-return',
		];
		return $map[$type] ?? '';
	}

}
