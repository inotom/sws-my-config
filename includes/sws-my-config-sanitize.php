<?php
/**
 * Serendip My Config make sanitizing function
 *
 * @package Serendip
 */

/**
 * サニタイジング関数を返す
 *
 * @param array $settings 設定.
 * @access public
 * @return function
 */
function sws_my_config_make_sanitizing_func( $settings ) {
	return function( $input ) use ( $settings ) {
		foreach ( $settings['fields'] as $field ) {
			// チェックボックスの場合
			if ( 'checkbox' === $field['type'] ) {
				$input[ $field['key'] ] = ( '1' === $input[ $field['key'] ] ) ? '1' : '0';
			}

			// カスタムサニタイズ
			if ( 'text1' === $field['key'] ) {
				if ( mb_strlen( $input[ $field['key'] ], 'UTF-8' ) > 10 ) {
					$text1_key = sprintf( '%s[%s]', SWS_MY_CONFIG_OPTION_KEY, $field['key'] );
					add_settings_error(
						$text1_key,
						$text1_key . '-error',
						__( $field['label'] . ' は10文字以上は保存できません。', SWS_MY_CONFIG_SLUG ),
						'error'
					);
					$input[ $field['key'] ] = get_option( SWS_MY_CONFIG_OPTION_KEY )[ $field['key'] ];
				}
			}
		}
		return $input;
	};
}
