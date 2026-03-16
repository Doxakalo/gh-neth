<?php

namespace common\services;

use Yii;

class Leaderboard 
{

	const CACHE_FILE = 'leaderboard.json';
	const CACHE_DIR = '@frontend/runtime/api-cache';

	/**
	 * Get leaderboard, return cached data if age is not expired
	 * 
	 * @return array
	 */
	public static function getLeaderboard() {
		$maxAge = Yii::$app->params['leaderboard.maxAge'] ?? 3600;
		$filePath = self::getFilePath();
		$modifiedTime = file_exists($filePath) ? filemtime($filePath) : null;
		if ($modifiedTime && (time() - $modifiedTime < $maxAge)) {
			// Return cached data
			return json_decode(file_get_contents($filePath), true);
		}

		// Generate new data and cache it
		$data = self::generate();
		self::writeCache($data);
		return $data;
	}


	/**
	 * Write array data as JSON to the cache file path
	 *
	 * @param array $data
	 * @return bool
	 */
	private static function writeCache(array $data) {
		$filePath = self::getFilePath();
		$json = json_encode($data, JSON_PRETTY_PRINT);
		return file_put_contents($filePath, $json) > 0 ? true : false;
	}


	/**
	 * Get (or create) the cache directory
	 * 
	 * @return string
	 */
	private static function getCacheDir() {
		$cacheDir = Yii::getAlias(self::CACHE_DIR);
		if (!is_dir($cacheDir)) {
			mkdir($cacheDir, 0775, true);
		}
		return $cacheDir;
	}


	/**
	 * Get the file path for the leaderboard cache
	 * 
	 * @return string
	 */
	private static function getFilePath(){
		$cacheDir = self::getCacheDir();
		return $cacheDir . DIRECTORY_SEPARATOR . self::CACHE_FILE;
	}


	/**
	 * Generate leaderboard data
	 * 
	 * @return array
	 */
	public static function generate(){
		$maxUsers = Yii::$app->params['leaderboard.maxUsers'] ?? 100;

		// Get top users by fund balance
		$topUsers = \common\models\User::find()
			->select([
				'user.id',
				'user.nickname',
				'SUM(transaction.amount) AS fund_balance'
			])
			->joinWith('transactions', false)
			->where(['user.type' => 0])
			->groupBy('user.id')
			->orderBy([
				'fund_balance' => SORT_DESC, 
				'user.nickname' => SORT_ASC
			])
			->limit($maxUsers)
			->asArray()
			->all();

		// generate output
		return [
			'updated_at' => time(),
			'leaderboard' => array_map(function($user) {
				return [
					'nickname' => $user['nickname'],
					'balance' => number_format((float)$user['fund_balance'], 2, '.', ''),
				];
			}, $topUsers),
		];
	}

}

