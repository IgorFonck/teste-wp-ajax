<?php
/*
Plugin Name: Teste WP AJAX
Description: Plugin de teste da integração de WP com AJAX.
Version: 0.1
Author: Igor Fonseca
Author URI: https://github.com/igorfonck
*/

/* Fonte (versão básica): https://www.smashingmagazine.com/2011/10/how-to-use-ajax-in-wordpress/ */

add_action("wp_ajax_my_user_vote", "my_user_vote"); // Usuários logados
add_action("wp_ajax_nopriv_my_user_vote", "my_must_login"); // Usuários não logados

/* Para usuários logados */
function my_user_vote() {

  // Verificação de segurança
   if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_user_vote_nonce")) {
      exit("Erro de verificação.");
   }

   // Contabiliza e registra o número de votos   
   $vote_count = get_post_meta($_REQUEST["post_id"], "votes", true);
   $vote_count = ($vote_count == '') ? 0 : $vote_count;
   $new_vote_count = $vote_count + 1;

   $vote = update_post_meta($_REQUEST["post_id"], "votes", $new_vote_count);

   // Result arrays
   if($vote === false) {
      $result['type'] = "error";
      $result['vote_count'] = $vote_count;
   }
   else {
      $result['type'] = "success";
      $result['vote_count'] = $new_vote_count;
   }

   // Detecta se a alçai foi iniciada por uma chamada AJAX
   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      // Prepara o array para o código JavaScript
      $result = json_encode($result);
      echo $result;
   }
   else {
      // Retorna o usuário ao endereço de onde veio
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }

   // Encerra o script, evitando um retorno de -1 junto com o resultado
   die();

}

/* Para usuários não logados. */
function my_must_login() {
   echo "É necessário fazer login para votar.";
   die();
}

/* Adiciona o JavaScript do plugin */
add_action( 'init', 'voter_script_enqueuer' );

function voter_script_enqueuer() {
   
  // Registra o script com a dependência jQuery
  wp_register_script( "my_voter_script", WP_PLUGIN_URL.'/teste-wp-ajax/my_voter_script.js', array('jquery') );
   
   // Disponibiliza a URL do AJAX para o script
   wp_localize_script( 'my_voter_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'my_voter_script' );

}

?>