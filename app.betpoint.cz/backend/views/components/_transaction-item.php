<?php

use common\models\Transaction;
use backend\utils\TransactionViewUtils;

/**
 * @var array $transaction Transaction model passed from the ListView as an array
 * @var int|string $key The key associated with this bet in the ListView
 */
?>

<tr data-key="<?= $key ?>">
	<td>
		<span class="transaction-tag-label <?= TransactionViewUtils::getTransactionTypeClassName($transaction['type']) ?>">
			<?= ($transaction['action']) ?>
		</span>
	</td>
	<td>
		<?= $transaction['description'] ?>
		<?php if (!empty($transaction['user_bet_id_hash'])): ?>
			<span class="secondary-info">
				(Bet #<?= $transaction['user_bet_id_hash'] ?>)
			</span>
		<?php endif; ?>
	</td>
	<td class="nowrap"><?= Yii::$app->formatter->asDatetime($transaction['created_at']) ?></td>
	<td class="tar">
		<span class="amount-value <?= $transaction['amount'] >= 0 ? 'positive' : 'negative' ?>">
			<?= $transaction['amount'] > 0 ? '+' : '' ?><?= Yii::$app->formatter->asCurrencyValue($transaction['amount'], true) ?>
		</span>
	</td>
</tr>
