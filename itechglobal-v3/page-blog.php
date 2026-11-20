<?php
/*
Template Name: Blog (iTech Global)
*/
get_header();
?>
<main>
  <section class="section" id="blog">
    <div class="container">
      <header class="section-head">
        <h1>Insights &amp; Updates</h1>
        <p>Thought leadership, implementation notes, and updates from iTech Global.</p>
      </header>

      <div class="grid3">
        <?php
          $blog_query = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => 9,
            'post_status' => 'publish',
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
          ));
        ?>
        <?php if ($blog_query->have_posts()) : ?>
          <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
            <article class="card reveal">
              <?php if (has_post_thumbnail()) : ?>
                <div class="blog-thumbnail">
                  <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_large', array('loading' => 'lazy', 'decoding' => 'async')); ?></a>
                </div>
              <?php endif; ?>
              <h3 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <p class="blog-date"><?php echo esc_html(get_the_date('F j, Y')); ?></p>
              <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22, '...')); ?></p>
              <p class="blog-read-more"><a href="<?php the_permalink(); ?>">Read more</a></p>
            </article>
          <?php endwhile; ?>
          <?php wp_reset_postdata(); ?>
        <?php else : ?>
          <p>No blog posts yet. Check back soon.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
