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
    ];

    function get_ids($args){

        global $wpdb;

        $this->args = $args;

        $clause_join = $this->get_clause_join($args);
        $clause_where = $this->get_clause_where($args);
        $clause_end = $this->get_clause_end($args);

        $query = "SELECT ID FROM {$wpdb->prefix}posts{$clause_join}{$clause_where}{$clause_end}";

        $result = $wpdb->get_results($query);

        $ids = [];
        foreach( $result as $row ) {
            $ids[] = $row->ID;
        }
    
        return $ids;
    }

    function get_count($args){
        
        global $wpdb;

        $this->args = $args;

        $clause_join = $this->get_clause_join($args);
        $clause_where = $this->get_clause_where($args);
        $clause_end = $this->get_clause_end($args);

        $query = "SELECT count(ID) as count FROM {$wpdb->prefix}posts{$clause_join}{$clause_where}{$clause_end}";

        $result = $wpdb->get_results($query);

        return $result[0]->count;
    }

    function get_clause_join($args){

        global $wpdb;

        $term_id = $this->get_arg('term_id');
        $meta_key = $this->get_arg('meta_key');

        $clause = '';

        if( $term_id !== null ) {
            $clause .= " LEFT JOIN {$wpdb->prefix}term_relationships ON object_id = ID";
        }
    
        if( $meta_key !== null ) {
            $clause .= " LEFT JOIN {$wpdb->prefix}postmeta ON post_id = ID";
        }

        return $clause;
    }

    function get_clause_where($args){

        $post_status = $this->get_arg('post_status');
        $post_type = $this->get_arg('post_type');
        $term_id = $this->get_arg('term_id');
        $meta_key = $this->get_arg('meta_key');
        $meta_value = $this->get_arg('meta_value');
        
        $clause = " WHERE post_status = '{$post_status}' AND post_type = '{$post_type}'";

        if( $term_id !== null ) {
            $query .= " AND term_taxonomy_id = {$term_id}";
        }
    
        if( $meta_key !== null ) {
            $query .= " AND meta_key = '{$meta_key}'";
        }
    
        if( $meta_value !== null ) {
            $query .= " AND meta_value = '{$meta_value}'";
        }
        
        return $clause;
    }

    function get_clause_end($args){
        
        $limit = $this->get_arg('limit');

        $clause = '';

        if( $limit !== null ) {
            $query .= " LIMIT {$limit}";
        }

        return $clause;
    }

    function get_arg( $key ){
        return $this->args[$key] ?? $this->defaults[$key];
    }

}