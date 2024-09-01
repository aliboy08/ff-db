<?php
class FF_DB {
    
    public $args = [];

    public $defaults = [
        'post_status' => 'publish',
        'post_type' => 'post',
        'term_id' => null,
        'meta_key' => null,
        'meta_value' => null,
        'limit' => null,
        'orderby' => 'post_date',
        'order' => 'DESC',
    ];
    
    public $t_posts;
    public $t_term_relationships;
    public $t_postmeta;
    
    function __construct() {
        global $wpdb;
        $this->t_posts = $wpdb->prefix .'posts';
        $this->t_term_relationships = $wpdb->prefix .'term_relationships';
        $this->t_postmeta = $wpdb->prefix .'postmeta';
    }
    
    function get_ids($args){

        $this->args = $args;
        global $wpdb;

        $query_after = $this->get_query_after($args);

        $query = "SELECT ID FROM {$this->t_posts}{$query_after}";
        $result = $wpdb->get_results($query);

        $ids = [];
        foreach( $result as $row ) {
            $ids[] = $row->ID;
        }
    
        return $ids;
    }

    function get_count($args){

        $this->args = $args;
        global $wpdb;

        $join = $this->get_query_join($args);
        $where = $this->get_query_where($args);

        $query_after = $join . $where;
        
        $query = "SELECT count(ID) as count FROM {$this->t_posts}{$query_after}";

        $result = $wpdb->get_results($query);

        return $result[0]->count;
    }

    function get_query_after($args){

        $join = $this->get_query_join($args);
        $where = $this->get_query_where($args);
        $order = $this->get_query_order($args);
        $end = $this->get_query_end($args);

        $query = $join . $where . $order . $end;
        
        return $query;
    }

    function select($select, $args){

        $this->args = $args;
        global $wpdb;
        
        $query_after = $this->get_query_after($args);

        $query = "SELECT {$select} FROM {$this->t_posts}{$query_after}";

        $result = $wpdb->get_results($query);

        return $result;
    }

    function get_query_join($args){

        $term_id = $this->get_arg('term_id');
        $meta_key = $this->get_arg('meta_key');

        $query = '';

        if( $term_id !== null ) {
            $query .= " LEFT JOIN {$this->t_term_relationships} ON object_id = ID";
        }
    
        if( $meta_key !== null ) {
            $query .= " LEFT JOIN {$this->t_postmeta} ON post_id = ID";
        }

        return $query;
    }

    function get_query_where($args){

        $post_status = $this->get_arg('post_status');
        $post_type = $this->get_arg('post_type');
        $term_id = $this->get_arg('term_id');
        $meta_key = $this->get_arg('meta_key');
        $meta_value = $this->get_arg('meta_value');
        
        $query = " WHERE post_status = '{$post_status}' AND post_type = '{$post_type}'";

        if( $term_id !== null ) {
            $query .= " AND {$this->t_term_relationships}.term_taxonomy_id = {$term_id}";
        }
    
        if( $meta_key !== null ) {
            $query .= " AND {$this->t_postmeta}.meta_key = '{$meta_key}'";
        }
    
        if( $meta_value !== null ) {
            $query .= " AND {$this->t_postmeta}.meta_value = '{$meta_value}'";
        }
        
        return $query;
    }

    function get_query_end($args){
        
        $limit = $this->get_arg('limit');

        $query = '';

        if( $limit !== null ) {
            $query .= " LIMIT {$limit}";
        }

        return $query;
    }

    function get_query_order($args){

        $orderby = $this->get_arg('orderby');
        $order = $this->get_arg('order');

        $query = '';

        if( $orderby !== null ) {
            $query .= " ORDER BY {$this->t_posts}.{$orderby} {$order}";
        }

        return $query;
    }

    function get_arg( $key ){
        return $this->args[$key] ?? $this->defaults[$key];
    }

    function get_pages( $posts_per_page, $args ){
        $count = $this->get_count( $args );
        $pages_count = ceil($count / $posts_per_page);
        return $pages_count;
    }

}
