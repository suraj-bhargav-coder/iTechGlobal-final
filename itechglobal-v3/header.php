<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/svg+xml" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/itech-global-icon.svg">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="logo-strip-full">
      <div class="container nav">
        <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="iTech Global home"><img class="brand-mark" src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/itech-site-icon.png" alt=""><span class="brand-name">iTech <strong>Global</strong></span></a>
        <button class="menu-btn" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="main-nav">
          <span class="menu-icon" aria-hidden="true"><span></span><span></span></span>
          <span class="menu-label">Menu</span>
        </button>
        <nav id="main-nav" class="nav-links" aria-label="Main navigation">
          <a href="<?php echo esc_url(home_url('/#services')); ?>">Services</a>
          <a href="<?php echo esc_url(home_url('/#technology')); ?>">Solutions</a>
          <a href="<?php echo esc_url(home_url('/#process')); ?>">How we work</a>
          <a href="<?php echo esc_url(home_url('/#about')); ?>">About</a>
          <a href="<?php echo esc_url(home_url('/#faq')); ?>">FAQ</a>
          <a class="nav-cta" href="<?php echo esc_url(home_url('/#contact')); ?>">Talk to our team <span aria-hidden="true">→</span></a>
        </nav>
      </div>
    </div>
  </header>
