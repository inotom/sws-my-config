<?php
/**
 * Serendip My Config settings
 *
 * @package Serendip
 */

return array(
	'sections' => array(
		array(
			'key' => 'sws-sample-settings-section',
			'label' => 'サンプル設定',
		),
		array(
			'key' => 'sws-other-settings-section',
			'label' => 'その他の設定',
		),
	),
	'fields' => array(
		array(
			'type' => 'text',
			'section' => 'sws-sample-settings-section',
			'key' => 'text1',
			'label' => 'Sample Text',
			'desc' => 'フィールドの説明。',
		),
		array(
			'type' => 'checkbox',
			'section' => 'sws-sample-settings-section',
			'key' => 'check1',
			'label' => 'Sample Checkbox',
			'desc' => 'チェックボックスの説明。',
		),
		array(
			'type' => 'text',
			'section' => 'sws-other-settings-section',
			'key' => 'other-text2',
			'label' => 'その他テキスト',
			'desc' => 'フィールドの説明。',
		),
	),
);
