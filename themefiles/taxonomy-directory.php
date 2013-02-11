<?php get_header(); ?>
  
  <?php $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy')); ?>
  <?php $directories = get_terms_by_level('directory', $term->term_id); ?>

  <?php
  $args = array('taxonomy' => 'directory', 'term' => $term->slug);

  //  assigning variables to the loop
  global $wp_query;
  $wp_query = new WP_Query($args);
  ?>

  <div id="content" class="page col-full">
    <div id="main" class="col-left">
	<br/>
      <div id="breadcrumbs">
        <?php print listings_term_heirarchy('directory', $term); ?>
      </div>
    
      <div <?php post_class(); ?>>
        <h1 class="tumblog-title">Listings in <?php print $term->name; ?></h1>

        <div class="entry">
          <?php if ($wp_query->have_posts()): ?>
            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
              <?php $post_terms = get_the_terms(get_the_ID(), 'directory'); ?>
              <?php /* if($post_terms[$term->term_taxonomy_id]->term_id == $term->term_id): */ ?>
              <?php if($post_terms[$term->term_id]->term_id == $term->term_id): ?>
                <div class="entry-content">
                  <h3>
                    <a href="<?php print get_permalink(); ?>"><?php the_title(); ?></a>
                  </h3>
                  <div class="the_content">
                    <?php //the_content('Read more...'); ?>
                  </div>
                </div>
              <?php endif; ?>
            <?php endwhile; ?>
          <?php endif; ?>

          <?php if (!empty($directories)): ?>
            <div style="clear: both"></div>
            <hr />
            <ul class="listings">
              <?php foreach($directories as $directory): ?>
                <li><a href="<?php print $directory->link; ?>"><?php print $directory->name; ?></a></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <?php get_sidebar(); ?>
  </div>

<?php get_footer(); ?>