<?php
/**
 * Plugin Name: Serendip My Config
 * Version: 1.0
 * Description: Add custom config page to admin.
 * Author: inotom <wdf7322@yahoo.co.jp>
 * Author URI: http://www.serendip.ws/
 * Plugin URI: https://github.com/inotom/sws-my-config
 * Text Domain: sws-my-config
 * Domain Path: /languages
 *
 * @package     Serendip
 * @license     GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * @copyright   2016 (c) Serendip
 * @link        http://www.serendip.ws/
 */

if ( ! function_exists( 'add_action' ) ) {
	echo 'This plugin needs add_action function';
	exit;
}

require_once dirname( __FILE__ ) . '/includes/sws-my-config-constants.php';

if ( is_admin() ) {

	require_once dirname( __FILE__ ) . '/includes/sws-my-config-sanitize.php';

	$settings = require_once dirname( __FILE__ ) . '/includes/sws-my-config-settings.php';

	/**
	 * 設定メニューに My Config ページを追加
	 */
	add_action( 'admin_menu', function() {
		add_options_page(
			__( 'My Config', SWS_MY_CONFIG_SLUG ),
			__( 'My Config', SWS_MY_CONFIG_SLUG ),
			'manage_options', // My Config ページを操作する権限.
			SWS_MY_CONFIG_SLUG,
			function () { // My Config ページにフォームを出力.
			?>
			<div class="wrap">
				<h2><?php esc_html_e( 'My Config', SWS_MY_CONFIG_SLUG ); ?></h2>
				<form action="options.php" method="POST">
					<?php
					do_settings_sections( SWS_MY_CONFIG_PAGE );
					settings_fields( SWS_MY_CONFIG_GROUP );
					submit_button();
					?>
				</form>
			</div>
			<?php
			}
		);
	} );

	/**
	 * My Config ページを作成
	 */
	add_action( 'admin_init', function() use ( $settings ) {
		// セクション の追加
		foreach ( $settings['sections'] as $section ) {
			add_settings_section(
				$section['key'], // セクション ID
				__( $section['label'], SWS_MY_CONFIG_SLUG ),
				function() {
				?>
				<p>セクションの説明。</p>
				<?php
				},
				SWS_MY_CONFIG_PAGE // 表示するページ
			);
		}
		// フィールドの追加
		foreach ( $settings['fields'] as $field ) {
			switch ( $field['type'] ) {
				case 'checkbox':
					sws_my_config_add_checkbox(
						$settings,
						$field
					);
				break;
				default:
					sws_my_config_add_text_field(
						$settings,
						$field
					);
				break;
			}
		}
		// 設定項目の登録
		register_setting(
			SWS_MY_CONFIG_GROUP, // 設定のグループ名
			SWS_MY_CONFIG_OPTION_KEY,   // フィールド
			sws_my_config_make_sanitizing_func( $settings ) // サニタイズを行う関数[オプション].
		);
	} );

	if ( ! function_exists( 'sws_my_config_add_text_field' ) ) {
		/**
		 * テキストフィールドを出力
		 *
		 * @param array $settings フォーム設定.
		 * @param array $field_setting テキストフィールド設定.
		 * @access public
		 * @return void
		 */
		function sws_my_config_add_text_field( $settings, $field_setting ) {
			$field_key = sprintf( '%s[%s]', SWS_MY_CONFIG_OPTION_KEY, $field_setting['key'] );
			add_settings_field(
				$field_key,
				__( $field_setting['label'], SWS_MY_CONFIG_SLUG ),
				function() use ( $field_key, $settings, $field_setting ) {
					$options = get_option( SWS_MY_CONFIG_OPTION_KEY );
					$value = ( ( false !== $options ) && array_key_exists( $field_setting['key'], $options ) ) ? $options[ $field_setting['key'] ] : '';
				?>
				<input class="regular-text" type="text" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<p class="description"><?php echo esc_html( $field_setting['desc'] ); ?></p>
				<?php
				},
				SWS_MY_CONFIG_PAGE,
				$field_setting['section'], // 表示するセクション ID
				array(
					'label_for' => $field_key,
				)
			);
		}
	}

	if ( ! function_exists( 'sws_my_config_add_checkbox' ) ) {
		/**
		 * チェックボックスを出力
		 *
		 * @param array $settings フォーム設定.
		 * @param array $field_setting テキストフィールド設定.
		 * @access public
		 * @return void
		 */
		function sws_my_config_add_checkbox( $settings, $field_setting ) {
			$field_key = sprintf( '%s[%s]', SWS_MY_CONFIG_OPTION_KEY, $field_setting['key'] );
			add_settings_field(
				$field_key,
				__( $field_setting['label'], SWS_MY_CONFIG_SLUG ),
				function() use ( $field_key, $settings, $field_setting ) {
					$options = get_option( SWS_MY_CONFIG_OPTION_KEY );
					$status = ( ( false !== $options ) && array_key_exists( $field_setting['key'], $options ) ) ? $options[ $field_setting['key'] ] : '0';
				?>
				<input type="checkbox" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" value="1" <?php checked( 1, $status ); ?>>
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo esc_html( $field_setting['desc'] ); ?></label>
				<?php
				},
				SWS_MY_CONFIG_PAGE, // 表示するページ
				$field_setting['section'] // 表示するセクション ID
			);
		}
	}
}

if ( ! function_exists( 'sws_my_config_get' ) ) {
	/**
	 * オプションの値を取得する
	 *
	 * @param string $field_key オプションのフィールドのキー.
	 * @access public
	 * @return mixed
	 * @throws Exception Throw exception オプションにキーが存在しない場合例外を投げる.
	 */
	function sws_my_config_get( $field_key ) {
		$option = get_option( SWS_MY_CONFIG_OPTION_KEY );
		if ( false !== $option ) {
			if ( ! array_key_exists( $field_key, $option ) ) {
				throw new Exception( sprintf( '"%s" is not existing key.', $field_key ) );
			}
			return $option[ $field_key ];
		}
	}
}

if ( ! function_exists( 'sws_my_config_checked' ) ) {
	/**
	 * オプションのチェックボックスの状態を取得する
	 *
	 * @param string $field_key オプションのフィールドのキー.
	 * @access public
	 * @return boolean
	 * @throws Exception Throw exception チェックボックスではない場合例外を投げる.
	 */
	function sws_my_config_checked( $field_key ) {
		$option = get_option( SWS_MY_CONFIG_OPTION_KEY );
		if ( false === $option ) {
			return false;
		}
		if ( array_key_exists( $field_key, $option ) ) {
			if ( '0' === $option[ $field_key ] ) {
				return false;
			}
			if ( '1' === $option[ $field_key ] ) {
				return true;
			}
		}
		throw new Exception( sprintf( '"%s" is not checkbox key.', $field_key ) );
	}
}
