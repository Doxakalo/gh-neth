<?php

namespace common\utils;

class ModelUtils {

	/**
	 * Add sub-model errors to model
	 *
	 * @param yii\base\Model $model model to add errors to
	 * @param yii\base\Model $submodel model to get errors from
	 */
	public static function addModelErrors($model, $submodel) {
		foreach ($submodel->getErrors() as $attribute => $errors) {
			foreach ($errors as $error) {
				$model->addError($attribute, $error);
			}
		}
	}
	
}
