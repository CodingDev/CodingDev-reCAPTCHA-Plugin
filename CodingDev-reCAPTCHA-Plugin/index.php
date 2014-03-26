<?php
/**
 * @package     CodingDev reCAPTCHA Plugin
 *
 * @wordpress-plugin
 * Plugin Name: CodingDev reCAPTCHA Plugin
 * Plugin URI:  http://codingdev.de/159/codingdev-recaptcha-plugin/
 * Description: This Plugin add a reCAPTCHA input on the Register form.
 * Version:     1.0
 * Author:      Ren&eacute; Preu&szlig;
 * Author URI:  http://codingdev.de/author/rene-preuss/
 */

if ( ! defined( 'WPINC' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}
define( 'CODINGDEV_REGISTER_PLUGIN_DIR', trailingslashit( dirname( __FILE__ ) ) );
define( 'CODINGDEV_REGISTER_PLUGIN_BASEDIR', trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );

$codingDevreCAPTCHALanguage['en_US']['login_error'] = '<strong>Error</strong>: Please type the reCAPTCHA-Code in the input field.';
$codingDevreCAPTCHALanguage['en_US']['settings_title'] = 'CodingDev reCAPTCHA Settings';
$codingDevreCAPTCHALanguage['en_US']['settings_setup_title'] = 'Setup reCAPTCHA';
$codingDevreCAPTCHALanguage['en_US']['settings_setup_notice'] = 'To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>.';
$codingDevreCAPTCHALanguage['en_US']['priate_key'] = 'Private Key';
$codingDevreCAPTCHALanguage['en_US']['public_key'] = 'Public Key';
$codingDevreCAPTCHALanguage['en_US']['language'] = 'Language';

$codingDevreCAPTCHALanguage['de_DE']['login_error'] = '<strong>Fehler</strong>: Bitte gebe den reCAPTCHA-Code in die Textbox ein.';
$codingDevreCAPTCHALanguage['de_DE']['settings_title'] = 'CodingDev reCAPTCHA Einstellungen';
$codingDevreCAPTCHALanguage['de_DE']['settings_setup_title'] = 'reCAPTCHA Einstellen';
$codingDevreCAPTCHALanguage['de_DE']['settings_setup_notice'] = 'Um reCAPTCHA zu nutzen musst du ein API-Key von <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a> holem.';
$codingDevreCAPTCHALanguage['de_DE']['priate_key'] = 'Privater Key';
$codingDevreCAPTCHALanguage['de_DE']['public_key'] = '&Ouml;ffentlicher Key';
$codingDevreCAPTCHALanguage['de_DE']['language'] = 'Sprache';


require_once('recaptchalib.php');

add_action('register_form', 'add_reCAPTCHA_field' );
add_action('register_post','check_fields_errors',10,3);

$publicKey = get_option( 'codingdev_recaptcha_privatekey');
$privateKey = get_option( 'codingdev_recaptcha_publickey');
$language = get_option( 'codingdev_recaptcha_lang');

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;



function add_reCAPTCHA_field() { 
	global $publicKey, $error, $codingDevreCAPTCHALanguage;
	echo '<script>document.getElementById("login").setAttribute("style","width:370px");</script>';
	echo recaptcha_get_html($publicKey, $error);
	echo "<br>";
}

function check_fields_errors($login, $email, $errors) {
	global $publicKey, $privateKey, $resp, $codingDevreCAPTCHALanguage, $language;
    if ($_POST["recaptcha_response_field"]) {
        $resp = recaptcha_check_answer ($privateKey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
			$errors->add('empty_recaptcha', $resp->error);
		}
	}else{
		$errors->add('empty_recaptcha', $codingDevreCAPTCHALanguage[$language]['login_error']);
	}
}

function wphub_register_settings() {
	add_option( 'codingdev_recaptcha_privatekey', '');
	add_option( 'codingdev_recaptcha_publickey', '');
	add_option( 'codingdev_recaptcha_lang', get_locale());
	register_setting( 'default', 'codingdev_recaptcha_privatekey' ); 
	register_setting( 'default', 'codingdev_recaptcha_publickey' ); 
	register_setting( 'default', 'codingdev_recaptcha_lang' ); 
} 
add_action( 'admin_init', 'wphub_register_settings' );
 
function wphub_register_options_page() {
	global $language;
	add_options_page($codingDevreCAPTCHALanguage[$language]['settings_title'], 'reCAPTCHA', 'manage_options', 'wphub-options', 'wphub_options_page');
}
add_action('admin_menu', 'wphub_register_options_page');

function selectedLanguage($this, $selected){
	if($this == $selected){
		return 'selected="selected"';
	}
}
function wphub_options_page() {
	global $codingDevreCAPTCHALanguage;
	?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo $codingDevreCAPTCHALanguage[get_option('codingdev_recaptcha_lang')]['settings_title'] ; ?></h2>
	<form method="post" action="options.php"> 
		<?php settings_fields( 'default' ); ?>
		<h3><?php echo $codingDevreCAPTCHALanguage[get_option('codingdev_recaptcha_lang')]['settings_setup_title'] ; ?></h3>
			<p><?php echo $codingDevreCAPTCHALanguage[get_option('codingdev_recaptcha_lang')]['settings_setup_notice']; ?></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="codingdev_recaptcha_publickey"><?php echo $codingDevreCAPTCHALanguage[get_option('codingdev_recaptcha_lang')]['public_key']; ?>:</label></th>
					<td><input type="text" style="width: 500px;" id="codingdev_recaptcha_publickey" name="codingdev_recaptcha_publickey" value="<?php echo get_option('codingdev_recaptcha_publickey'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="codingdev_recaptcha_privatekey"><?php echo $codingDevreCAPTCHALanguage[get_option('codingdev_recaptcha_lang')]['priate_key']; ?>:</label></th>
					<td><input type="text" style="width: 500px;" id="codingdev_recaptcha_privatekey" name="codingdev_recaptcha_privatekey" value="<?php echo get_option('codingdev_recaptcha_privatekey'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="codingdev_recaptcha_lang"><?php echo $codingDevreCAPTCHALanguage[get_option('codingdev_recaptcha_lang')]['language']; ?>:</label></th>
					<td>
					<?php
						echo '<select id="codingdev_recaptcha_lang" name="codingdev_recaptcha_lang" size="1">';
						echo '<option '.selectedLanguage("en_US", get_option('codingdev_recaptcha_lang')).' value="en_US">English</option>';
						echo '<option '.selectedLanguage("de_DE", get_option('codingdev_recaptcha_lang')).' value="de_DE">Deutsch</option>';
						echo '</select>';
					?>
					</td>
				</tr>
			</table>
		<?php submit_button(); ?>
	</form>
</div>
<?php
}
?>