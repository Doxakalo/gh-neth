<?php

namespace common\utils\mail;

use Yii;
use \Pelago\Emogrifier\CssInliner;
use \Html2Text\Html2Text;

/**
 * Mail utilities for Yii2
 * 
 * Author: Ludvik Michalek
 * Version: 2.2
 */
class MailUtils {

	/**
	 * Do CSS inlining in HTML
	 * 
	 * @param string $html
	 * @param string $cssFile
	 * @return string
	 */
	public static function inlineHtmlCss($html, $cssFile){
		if(file_exists($cssFile)) {
			$css = file_get_contents($cssFile);
			$htmlInline = CssInliner::fromHtml($html)->inlineCss($css)->render();
			return $htmlInline;
		} else {
			return $html;
		}
	}
	
	
	/**
	 * Remove script tag from HTML
	 * 
	 * @param string $html
	 * @return string
	 */
	public static function removeScriptTags($html){
		return preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
	}
	
	
	/**
	 * Get plain text from html
	 * 
	 * @param string $html
	 * @return string
	 */
	public static function toPlainText($html){
		$htmlToText = new Html2Text($html);
		return $htmlToText->getText();		
	}


	/**
	 * Get base64 encoded image from file
	 * 
	 * @param string $file
	 * @return string|null
	 */
	public static function imageToBase64($file){
		$path = Yii::getAlias('@common/mail/images/' . $file); 
		if(file_exists($path)) {
			return base64_encode(file_get_contents($path));
		}
		return null;
	}

	
}
