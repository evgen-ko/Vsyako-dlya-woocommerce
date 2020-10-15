<?php
/**
 * @package Download photo link
 * @version 1.0.0
 */
/*
Plugin Name: Скачать фото по ссылке
Plugin URI: https://plastilin-st.ru
Description: Поиск по номеру заказа и переход на файлообменник для скачивания исходников заказа. Для вавода необходимо добавить шорткод  [download_photo] в нужном месте.
Author: Evgen
Version: 1.0.0

*/

# Выход при прямом доступе
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
  
 

   function my_download_photo_code(){
    
  ?>
<form method="post" value="" >
<input type = "txt" name="photo_order_number" placeholder="Введите номер заказа " data-tilda-req="1" data-tilda-rule="none" style="color:#000000; border:1px solid #000000; padding: 10px; ">
<?php wp_nonce_field('photo_order_number_my_action','photo_order_number_nonce_field'); // защитное скрытое поле ?>
<input type="submit" class="t-submit" style="color:#ffffff; background-color:#2e93ff; font-family:Ubuntu; padding: 10px; border: solid 1px #000000;"> 
</form>

<script>
   // Обнуляум сессию, защита от повторной отправки формы
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

<?php 
//global $link;
 $sku = isset( $_POST['photo_order_number']) ? $_POST['photo_order_number'] : ''; // Получаем Артикул методом POST из поля ввода
$product_id = wc_get_product_id_by_sku( $sku ); // Получаем ID по Артиклу
$link = get_permalink( $product_id );           // Получаем ссылку по ID



if (isset ($_POST['photo_order_number'])) {
	
	// Проверяем скрытые поля
	if ( empty($_POST) || ! wp_verify_nonce( $_POST['photo_order_number_nonce_field'], 'photo_order_number_my_action') ){
   print 'Извините, проверочные данные не соответствуют.';
   exit;
}
else {

//---
  
if($product_id > 0){ // если существует ID товара
	$product = wc_get_product($product_id);
   $product_url = $product -> get_product_url();  // Получаем URL внешнего товара
	echo  '<meta http-equiv="refresh" content="3; url= ' .$link. '">' ; // Редирект на страницу товара
	
	print_r ($product_url ); 
	
   }
   if(! $product_id && $sku > 0 ) { // Если отсутствует ID или Артикул товара выводим сообщение
	wc_print_notice( 
                sprintf(  'Заказа с номером "' .$sku. '"  не существует!'), 'error' ) ; 
   } else { echo '';}
   }   
}
  }
  
   // Проверяем установлен ли Woocommerce.
if ( in_array(
	'woocommerce/woocommerce.php',
	apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
	true
) ) {
	// Если установлен Woocommerce добавляем шорткод  [download_photo]
	add_shortcode( 'download_photo', 'my_download_photo_code');
} else {
	add_action( 'admin_notices', 'dfl_not_woocommerce_notice' );
	return false;
}
    // Если не установлен Woocommerce выводим сообщение об ошибке.
function dfl_not_woocommerce_notice(){
	$dfl_message = "Для работы плагина <b>\"Скачать фото по ссылке\" </b> обязательным условием является установка и активация Woocommerce ";
	echo '<div class="notice notice-error is-dismissible"> <p>'. $dfl_message .'</p></div>';
}
   
   ?>