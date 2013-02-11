<?php get_header(); ?>
  
  <?php $directories = get_terms_by_level('directory', 0); ?>

  <div id="content" class="page col-full">
    <div id="main" class="col-left">
   
      <div <?php post_class(); ?>>
        <h1 class="tumblog-title"><?php the_title(); ?></h1>

        <div class="entry">
          <ul>
            <?php foreach($directories as $directory): ?>
              <?php //if ($directory->count): ?>
                <li><a href="<?php print $directory->link; ?>"><?php print $directory->name; ?></a></li>
              <?php //endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <?php get_sidebar(); ?>
  </div>

<?php get_footer(); ?>