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

/**
 * 設定メニューに My Config ページを追加
 */
add_action( 'admin_menu', function() {
	$settings = require dirname( __FILE__ ) . '/includes/sws-my-config-settings.php';
	add_options_page(
		__( 'My Config', $settings['slug'] ),
		__( 'My Config', $settings['slug'] ),
		'manage_options', // My Config ページを操作する権限.
		$settings['slug'],
		'sws_my_config_settings_page_callback'
	);
} );

/**
 * My Config ページにフォームを出力
 *
 * @access public
 * @return void
 */
function sws_my_config_settings_page_callback() {
	$settings = require dirname( __FILE__ ) . '/includes/sws-my-config-settings.php';
?>
<div class="wrap">
	<h2><?php esc_html_e( 'My Config', $settings['slug'] ); ?></h2>
	<form action="options.php" method="POST">
		<?php
		do_settings_sections( $settings['page'] );
		settings_fields( $settings['group'] );
		submit_button();
		?>
	</form>
</div>
<?php
}

/**
 * My Config ページを作成
 */
add_action( 'admin_init', function() {
	$settings = require dirname( __FILE__ ) . '/includes/sws-my-config-settings.php';
	$sample = $settings['fields']['sample'];
	// セクション (Sample Settings) を追加
	add_settings_section(
		'sws-sample-settings-section', // セクション ID
		__( 'Sample Settings', $settings['slug'] ),
		'sws_my_config_sample_settings_section_callback',
		$settings['page'] // 表示するページ
	);
	// セクション (Sample Settings) にフィールド (Sample Text) を追加
	add_settings_field(
		$sample['key'],
		__( $sample['label'], $settings['slug'] ),
		'sws_my_config_sample_settings_field_callback',
		$settings['page'], // 表示するページ
		'sws-sample-settings-section', // 表示するセクション ID
		array(
			'label_for' => $sample['key'],
		)
	);
	// 設定項目の登録
	register_setting(
		$settings['group'],    // 設定のグループ名
		$sample['key'], // フィールド
		'sws_my_config_sample_settings_field_sanitizing_callback' // サニタイズを行う関数[オプション].
	);
} );

/**
 * Sample Settings セクションを出力
 *
 * @access public
 * @return void
 */
function sws_my_config_sample_settings_section_callback() {
?>
<p>セクションの説明。</p>
<?php
}

/**
 * Sample Text フィールドを出力
 *
 * @access public
 * @return void
 */
function sws_my_config_sample_settings_field_callback() {
	$settings = require dirname( __FILE__ ) . '/includes/sws-my-config-settings.php';
	$sample = $settings['fields']['sample'];
?>
<input class="regular-text" type="text" name="<?php echo esc_attr( $sample['key'] ); ?>" id="<?php echo esc_attr( $sample['key'] ); ?>" value="<?php form_option( $sample['key'] ); ?>">
<p class="description">フィールドの説明。</p>
<?php
}

/**
 * Sample Text フィールドの値をサニタイジングする
 *
 * @param string $input フィールドの入力値.
 * @access public
 * @return string
 */
function sws_my_config_sample_settings_field_sanitizing_callback( $input ) {
	$settings = require dirname( __FILE__ ) . '/includes/sws-my-config-settings.php';
	$sample = $settings['fields']['sample'];
	if ( mb_strlen( $input, 'UTF-8' ) > 10 ) {
		add_settings_error(
			$sample['key'],
			$sample['key'] . '-error',
			__( $sample['label'] . ' は10文字以上は保存できません。', $settings['slug'] ),
			'error'
		);
		$input = get_option( $sample['key'] );
	}
	return $input;
}
