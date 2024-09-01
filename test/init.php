<?php
add_action('wp_footer', function(){

    $db = new FF_DB();
    $post_ids = $db->get_ids(['post_type' => 'post']);
    $count = $db->get_count(['post_type' => 'post']);

    pre_debug([
        'ids' => $post_ids,
        'count' => $count,
    ]);
});