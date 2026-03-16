<?php

use common\models\UserBet;
use backend\utils\BetViewUtils;

/**
 * @var array $bet Bet model passed from the ListView as an array
 * @var int|string $key The key associated with this bet in the ListView
 */
?>

<div class="bet-item <?= BetViewUtils::getBetStatusClassName($bet['status']) ?>" data-key="<?= $key ?>">
	<div class="column info">
		<div class="basic-info">
			<div class="group match-info">
				<div class="sport-name">
					<?= $bet['oddObj']['sportMatch']['sport']['name'] ?>
					/ 
					<?= $bet['oddObj']['sportMatch']['category']['name'] ?>

				</div>
				<div class="match-name-with-date">
					<h2 class="match-name">
						<span class="field">
							<span class="label">Home</span>
							<?= $bet['oddObj']['sportMatch']['home'] ?>&nbsp;/&nbsp;
						</span>
						<span class="field">
							<span class="label">Away</span>
							<?= $bet['oddObj']['sportMatch']['away'] ?>
						</span>
					</h2>
					<span class="match-date" title="Match start date/time">
						<?= Yii::$app->formatter->asDatetime($bet['oddObj']['sportMatch']['match_start']) ?>
					</span>
				</div>
			</div>
			<div class="group bet-type">
				<div class="field">
					<span class="label">Bet:</span>
					<strong><?= $bet['oddObj']['oddBetType']['name'] ?> - <?= $bet['oddObj']['name'] ?></strong>
				</div>
			</div>
			<div class="group bet-values">
				<div class="field horizontal">
					<span class="label">Odd:</span> 
					<strong><?= Yii::$app->formatter->asDecimal($bet['odd_value']) ?></strong>
				</div>
				<div class="field horizontal">
					<span class="label">Bet Amount:</span> 
					<strong><?= Yii::$app->formatter->asCurrencyValue($bet['amount']) ?></strong>
				</div>
			</div>
		</div>
		<div class="detail">
			<div class="group date-info">
				<div class="field">
					<span class="label">Match date:</span>
					<strong>
						<?= Yii::$app->formatter->asDatetime($bet['oddObj']['sportMatch']['match_start']) ?>
					</strong>
				</div>
				<div class="field">
					<span class="label">Bet date:</span>
					<strong>
						<?= Yii::$app->formatter->asDatetime($bet['created_at']) ?>
					</strong>
				</div>
				<div class="field">
					<span class="label">Bet ID:</span>
					<strong>
						#<?= $bet['id_hash'] ?>
					</strong>
				</div>
			</div>
		</div>
	</div>
	<div class="column status <?= BetViewUtils::getBetStatusClassName($bet['status']) ?>">

		<?php if (BetViewUtils::isBetEvaluated($bet['status'])): ?>
			<span class="amount-label">
				<?= BetViewUtils::getBetResultAmountFormatted($bet) ?>
			</span>
		<?php endif; ?>

		<span class="status-label">
			<?= BetViewUtils::getBetStatusText($bet['status']) ?>
		</span>

		<?php if ($bet['status'] === UserBet::STATUS_PENDING): ?>
			<span class="status-label tiny">
				Potential Win: 
				<?= Yii::$app->formatter->asCurrencyValue(BetViewUtils::getBetPotentialWin($bet['amount'], $bet['odd_value'])) ?>
			</span>
		<?php endif; ?>

	</div>
	<div class="column toggle">
		<button class="toggle-button" title="Show detail"><i class="icon sbc-icon-caret-right"></i></button>
	</div>
</div>
