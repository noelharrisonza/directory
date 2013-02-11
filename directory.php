<?php

error_reporting(E_ALL &~ E_NOTICE);
ini_set('display_errors', true);

/*
  Plugin Name: Directories
  Plugin URI: http://spinningyourweb.net
  Description: Create directory listings of things.
  Version: 1.0
  Author: Spinning Your Web
  Author URI: http://spinningyourweb.net
  License: GPL2
*/

/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_directory_taxonomy() {
  // Add new "Directories" taxonomy to Posts
  register_taxonomy('directory', 'listing', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x('Directory', 'taxonomy general name'),
      'singular_name' => _x('Directory', 'taxonomy singular name'),
      'search_items' =>  __('Search Directories'),
      'all_items' => __('All Directories'),
      'parent_item' => __('Parent Director'),
      'parent_item_colon' => __('Parent Directory:'),
      'edit_item' => __('Edit Directory'),
      'update_item' => __('Update Directory'),
      'add_new_item' => __('Add New Directory'),
      'new_item_name' => __('New Directory Name'),
      'menu_name' => __('Directories'),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'directories', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/directories/"
      'hierarchical' => true // This will allow URL's like "/directories/boston/cambridge/"
    ),
  ));
}
add_action('init', 'add_directory_taxonomy', 0);

// Now we make a custom page to display.
function listing_post_type() {
  register_post_type('listing',
    array(
      'labels' => array(
        'name' => __('Listings'),
        'singular_name' => __('Listing'),
        'all_items' => __('All Listings'),
        'add_new_item' => __('Add New Listing'),
        'edit_item' => __('Edit Listing'),
      ),
      'public' => true,
      'has_archive' => true,
      'taxonomies' => array(
        'directory',
      ),
    )
  );
}
add_action('init', 'listing_post_type');

//Template fallback
function my_theme_redirect() {
  global $wp;
  $plugindir = dirname(__FILE__);

  // Get the template for the directories.
  if (!empty($wp->query_vars['directory'])) {
    $templatefilename = 'taxonomy-directory.php';

    if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
      $return_template = TEMPLATEPATH . '/' . $templatefilename;
    }
    else {
      $return_template = $plugindir . '/themefiles/' . $templatefilename;
    }

    do_theme_redirect($return_template);
  }
  elseif ($wp->query_vars["pagename"] == 'directory') {
    $templatefilename = 'page-directory.php';
    
    if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
      $return_template = TEMPLATEPATH . '/' . $templatefilename;
    }
    else {
      $return_template = $plugindir . '/themefiles/' . $templatefilename;
    }
    
    do_theme_redirect($return_template);
  }

  print $return_template;
}
add_action("template_redirect", 'my_theme_redirect');

function do_theme_redirect($url) {
  global $post, $wp_query;

  if (have_posts()) {
    include($url);
    die();
  }
  else {
    $wp_query->is_404 = true;
  }
}

// We want to build a taxonomy heirarchy for breadcrumbs and shit.
function listings_term_heirarchy($taxonomy, $active_term) {
  $parents = get_term_parents($active_term->term_id, $taxonomy, true, '&raquo;');
  
  $parents = rtrim($parents, ' &raquo; ');
  return '<a href="'. get_settings('siteurl') .'/directory">Directory</a> &raquo; '. $parents;
}

function get_term_parents($id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array()) {
    $chain = '';
    $parent = &get_term($id, $taxonomy);
    
    if (is_wp_error($parent)) {
      return $parent;
    }

    if ($nicename) {
      $name = $parent->slug;
    }
    else {
      $name = $parent->name;
    }

    if ($parent->parent && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited)) {
      $visited[] = $parent->parent;
      $chain .= get_term_parents($parent->parent, $taxonomy, $link, $separator, $nicename, $visited);
    }

    if ($link) {
      $chain .= '<a href="' . get_term_link( $parent->slug, $taxonomy ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) . '">'.$name.'</a> ' . $separator .' ';
    }
    else {
      $chain .= $name.$separator;
    }

    return $chain;
}

// Get all the terms for the directory.
function get_directory_terms($taxonomy = 'directory') {
  $raw_terms = get_terms($taxonomy);

  foreach ($raw_terms as $term) {
    // Add the term link.
    $term->link = get_term_link($term->slug, $taxonomy);

    // Then build a new array.
    $all_terms[$term->slug] = $term;
  }

  // Then return everything.
  return $all_terms;
}

function get_terms_by_level($taxonomy, $parent = 0) {
  $terms = get_terms($taxonomy);

  foreach ($terms as $term) {
    if ($term->parent == $parent) {
      $term->link = get_term_link($term->slug, $taxonomy);
      $the_terms[$term->slug] = $term;
    }
  }

  return $the_terms;
}