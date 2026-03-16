<?php

namespace backend\utils;

use Yii;
use common\models\UserBet;

class BetViewUtils {

	/**
	 * Returns the CSS class name for the bet status.
	 *
	 * @param int $status The status of the bet.
	 * @return string The CSS class name corresponding to the bet status.
	 */
	public static function getBetStatusClassName($status) {
		switch ($status) {
			case UserBet::STATUS_WIN:
				return 'status-win';
			case UserBet::STATUS_LOSS:
				return 'status-loss';
			case UserBet::STATUS_CANCELLED:
				return 'status-cancelled';
			case UserBet::STATUS_PENDING:
			default:
				return 'status-pending';
		}
	}


	/**
	 * Checks if the bet has been evaluated (win or loss).
	 *
	 * @param int $status The status of the bet.
	 * @return bool True if the bet is evaluated, false otherwise.
	 */
	public static function isBetEvaluated($status) {
		return in_array($status, [
			UserBet::STATUS_WIN,
			UserBet::STATUS_LOSS
		], true);
	}


	/**
	 * Returns the text representation of the bet status.
	 *
	 * @param int $status The status of the bet.
	 * @return string The text representation of the bet status.
	 */
	public static function getBetStatusText($status) {
		switch ($status) {
			case UserBet::STATUS_WIN:
				return 'Won';
			case UserBet::STATUS_LOSS:
				return 'Lost';
			case UserBet::STATUS_CANCELLED:
				return 'Cancelled';
			case UserBet::STATUS_PENDING:
			default:
				return 'Not Finished';
		}
	}


	/**
	 * Formats the bet result amount based on the bet status.
	 *
	 * @param array $bet The bet data containing status, amount, and odd_value.
	 * @return string Formatted amount
	 */
	public static function getBetResultAmountFormatted($bet) {
		switch ($bet['status']) {
			case UserBet::STATUS_WIN:
				$amount = $bet['amount'] * $bet['odd_value'];
				break;
			case UserBet::STATUS_LOSS:
				$amount = $bet['amount'] * -1;
				break;
			default:
				$amount = 0;
		}
		$formatted = Yii::$app->formatter->asCurrencyValue($amount);
		return $amount > 0 ? '+' . $formatted : $formatted;
	}


	/**
	 * Calculate potential win amount for a bet.
	 *
	 * @param float $amount
	 * @param float $oddValue
	 * @return float
	 */
	public static function getBetPotentialWin($amount, $oddValue) {
		return $amount * $oddValue;
	}



}
