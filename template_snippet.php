<?php

  /* This snippet should be added to the theme's single.php template
  * (or another desired template) to show the Vote option and vote count */

  $votes = get_post_meta($post->ID, "votes", true);
  $votes = ($votes == "") ? 0 : $votes;
  ?>
  Este post tem <span id='vote_counter'><?php echo $votes ?></span> voto(s)<br>
  <?php
  $nonce = wp_create_nonce("my_user_vote_nonce");
  $link = admin_url('admin-ajax.php?action=my_user_vote&post_id='.$post->ID.'&nonce='.$nonce);
  echo '<a class="user_vote" data-nonce="' . $nonce . '" data-post_id="' . $post->ID . '" href="' . $link . '">Votar neste artigo</a>';

?>
