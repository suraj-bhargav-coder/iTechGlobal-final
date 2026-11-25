<?php
function itechglobal_enqueue_assets() {
  $theme_css    = get_template_directory() . '/assets/css/theme.css';
  $redesign_css = get_template_directory() . '/assets/css/redesign.css';
  $theme_js     = get_template_directory() . '/assets/js/theme.js';

  wp_enqueue_style(
    'itechglobal-theme',
    get_template_directory_uri() . '/assets/css/theme.css',
    array(),
    file_exists($theme_css) ? (string) filemtime($theme_css) : null
  );
  wp_enqueue_style(
    'itechglobal-redesign',
    get_template_directory_uri() . '/assets/css/redesign.css',
    array(),
    file_exists($redesign_css) ? (string) filemtime($redesign_css) : null
  );
  wp_enqueue_script(
    'itechglobal-theme',
    get_template_directory_uri() . '/assets/js/theme.js',
    array(),
    file_exists($theme_js) ? (string) filemtime($theme_js) : null,
    true
  );
}
add_action('wp_enqueue_scripts', 'itechglobal_enqueue_assets');



function itechglobal_disable_frontend_emojis() {
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');
}
add_action('init', 'itechglobal_disable_frontend_emojis');

function itechglobal_brand_identity_markup() {
  $logo = get_template_directory_uri() . '/assets/itech-logo.png';
  $icon = get_template_directory_uri() . '/assets/itech-logo.png';
  $site_url = home_url('/');

  $organization = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'iTech Global',
    'url' => $site_url,
    'logo' => array(
      '@type' => 'ImageObject',
      'url' => $logo,
      'width' => 512,
      'height' => 512,
    ),
  );

  $website = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'iTech Global',
    'alternateName' => 'iTech',
    'url' => $site_url,
  );

  printf(
    "<link rel=\"icon\" type=\"image/png\" sizes=\"512x512\" href=\"%1\$s\">\n",
    esc_url($icon)
  );
  printf(
    "<meta property=\"og:site_name\" content=\"iTech Global\">\n<meta property=\"og:image\" content=\"%1\$s\">\n<meta property=\"og:image:width\" content=\"512\">\n<meta property=\"og:image:height\" content=\"512\">\n<meta name=\"twitter:card\" content=\"summary\">\n<meta name=\"twitter:image\" content=\"%1\$s\">\n",
    esc_url($logo)
  );
  echo '<script type="application/ld+json">' .
    wp_json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
    '</script>' . "\n";
  echo '<script type="application/ld+json">' .
    wp_json_encode($website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
    '</script>' . "\n";
}
add_action('wp_head', 'itechglobal_brand_identity_markup', 2);

function itechglobal_defer_scripts($tag, $handle, $src) {
  if (in_array($handle, array('itechglobal-theme', 'itechglobal-contact-form'), true)) {
    if (strpos($tag, ' defer') === false) {
      return str_replace(' src=', ' defer src=', $tag);
    }
  }

  return $tag;
}
add_filter('script_loader_tag', 'itechglobal_defer_scripts', 10, 3);

function itechglobal_theme_support() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', array('caption', 'comment-form', 'comment-list', 'gallery', 'search-form', 'style', 'script'));
}
add_action('after_setup_theme', 'itechglobal_theme_support');

function itechglobal_document_title_parts($title) {
  $title['site'] = 'iTech Global';
  return $title;
}
add_filter('document_title_parts', 'itechglobal_document_title_parts');

function itechglobal_meta_description() {
  if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION')) {
    return;
  }

  if (is_front_page()) {
    $description = get_theme_mod(
      'itechglobal_hero_subtitle',
      'iTech Global helps organizations connect business systems, modernize cloud environments, and develop digital products for practical business needs.'
    );
  } elseif (is_page_template('page-blog.php')) {
    $description = 'Insights, implementation guidance, and technology updates from iTech Global on EDI, cloud modernization, and digital product engineering.';
  } elseif (is_singular()) {
    $description = get_the_excerpt();
  } elseif (is_home() || is_archive()) {
    $description = get_bloginfo('description');
  } else {
    $description = '';
  }

  $description = trim(wp_strip_all_tags($description));
  if ($description !== '') {
    printf("<meta name=\"description\" content=\"%s\">\n", esc_attr(wp_trim_words($description, 30, '')));
  }
}
add_action('wp_head', 'itechglobal_meta_description', 1);

function itechglobal_robots_txt($output, $public) {
  if (!$public || strpos($output, 'Sitemap:') !== false) {
    return $output;
  }

  return rtrim($output) . "\nSitemap: " . esc_url_raw(home_url('/wp-sitemap.xml')) . "\n";
}
add_filter('robots_txt', 'itechglobal_robots_txt', 10, 2);

function itechglobal_serve_llms_txt($wp) {
  if (empty($_SERVER['REQUEST_URI'])) {
    return;
  }

  $request_path = wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH);
  $llms_path = wp_parse_url(home_url('/llms.txt'), PHP_URL_PATH);

  if (!$request_path || !$llms_path || untrailingslashit($request_path) !== untrailingslashit($llms_path)) {
    return;
  }

  $llms_file = get_template_directory() . '/llms.txt';
  if (!is_readable($llms_file)) {
    status_header(404);
    exit;
  }

  status_header(200);
  header('Content-Type: text/plain; charset=utf-8');
  header('Cache-Control: public, max-age=3600');
  readfile($llms_file);
  exit;
}
add_action('parse_request', 'itechglobal_serve_llms_txt');

function itechglobal_customize_register($wp_customize) {
  $wp_customize->add_section('itechglobal_home', array(
    'title' => 'iTech Global: Home Page',
    'priority' => 30,
  ));

  $wp_customize->add_setting('itechglobal_hero_title', array(
    'default' => 'Connect the systems your business depends on.',
    'sanitize_callback' => 'sanitize_text_field',
  ));
  $wp_customize->add_control('itechglobal_hero_title', array(
    'label' => 'Hero Title',
    'section' => 'itechglobal_home',
    'type' => 'text',
  ));

  $wp_customize->add_setting('itechglobal_hero_subtitle', array(
    'default' => 'iTech Global helps organizations connect trading partners and core applications, modernize cloud environments, and build digital products for real business needs.',
    'sanitize_callback' => 'sanitize_textarea_field',
  ));
  $wp_customize->add_control('itechglobal_hero_subtitle', array(
    'label' => 'Hero Subtitle',
    'section' => 'itechglobal_home',
    'type' => 'textarea',
  ));

  $wp_customize->add_setting('itechglobal_contact_email', array(
    'default' => 'info@itechglobal.in',
    'sanitize_callback' => 'sanitize_email',
  ));
  $wp_customize->add_control('itechglobal_contact_email', array(
    'label' => 'Contact Email',
    'section' => 'itechglobal_home',
    'type' => 'email',
  ));

  $wp_customize->add_setting('itechglobal_contact_phone', array(
    'default' => '+91 90000 00000',
    'sanitize_callback' => 'sanitize_text_field',
  ));
  $wp_customize->add_control('itechglobal_contact_phone', array(
    'label' => 'Contact Phone',
    'section' => 'itechglobal_home',
    'type' => 'text',
  ));

  $wp_customize->add_setting('itechglobal_contact_address', array(
    'default' => 'Hyderabad, Telangana, India',
    'sanitize_callback' => 'sanitize_textarea_field',
  ));
  $wp_customize->add_control('itechglobal_contact_address', array(
    'label' => 'Contact Address',
    'section' => 'itechglobal_home',
    'type' => 'textarea',
  ));
}
add_action('customize_register', 'itechglobal_customize_register');

// Load contact form modules
require_once get_template_directory() . '/inc/contact-db.php';
require_once get_template_directory() . '/inc/contact-form-handler.php';
require_once get_template_directory() . '/inc/contact-admin-page.php';

function itechglobal_tech_icon($icon) {
  $icons = array(
    'exchange'   => '<path d="M7 7h12l-4-4"/><path d="M17 17H5l4 4"/><path d="M19 7 5 21"/>',
    'api'        => '<path d="M7 8h10"/><path d="M7 16h10"/><path d="M9 4 5 20"/><path d="M19 4l-4 16"/>',
    'network'    => '<circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M8.7 10.7 15.3 7.3"/><path d="M8.7 13.3 15.3 16.7"/>',
    'gateway'    => '<rect x="4" y="5" width="16" height="14" rx="2"/><path d="M8 9h8"/><path d="M8 13h5"/><path d="M12 19v3"/>',
    'file'       => '<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v6h5"/><path d="M9 14h6"/><path d="M9 18h6"/>',
    'portal'     => '<path d="M5 4h14v16H5z"/><path d="M9 8h6"/><path d="M9 12h6"/><path d="M9 16h3"/>',
    'cart'       => '<path d="M5 6h2l2 10h8l2-7H8"/><circle cx="10" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/>',
    'cloud'      => '<path d="M7 18h10a4 4 0 0 0 .6-7.95A6 6 0 0 0 6.2 12 3 3 0 0 0 7 18z"/>',
    'browser'    => '<rect x="4" y="5" width="16" height="14" rx="2"/><path d="M4 9h16"/><path d="M8 13h4"/><path d="M8 16h8"/>',
    'code'       => '<path d="m9 8-4 4 4 4"/><path d="m15 8 4 4-4 4"/><path d="M13 5 11 19"/>',
    'apps'       => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>',
    'database'   => '<ellipse cx="12" cy="5" rx="7" ry="3"/><path d="M5 5v7c0 1.7 3.1 3 7 3s7-1.3 7-3V5"/><path d="M5 12v7c0 1.7 3.1 3 7 3s7-1.3 7-3v-7"/>',
    'automation' => '<path d="M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8z"/><path d="M12 2v3"/><path d="M12 19v3"/><path d="M2 12h3"/><path d="M19 12h3"/><path d="m4.9 4.9 2.1 2.1"/><path d="m17 17 2.1 2.1"/><path d="m19.1 4.9-2.1 2.1"/><path d="m7 17-2.1 2.1"/>',
    'spark'      => '<path d="M12 2 14.2 8.2 20 10.5 14.2 12.8 12 19 9.8 12.8 4 10.5 9.8 8.2z"/>',
    'data'       => '<path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/><path d="M8 4v16"/><path d="M16 4v16"/>',
    'workflow'   => '<rect x="3" y="5" width="6" height="6" rx="1"/><rect x="15" y="13" width="6" height="6" rx="1"/><path d="M9 8h3a4 4 0 0 1 4 4v1"/>',
    'services'   => '<path d="M5 7h6v6H5z"/><path d="M13 11h6v6h-6z"/><path d="M8 13v4h5"/><path d="M11 10h2"/>',
    'saas'       => '<path d="M6 18h12a4 4 0 0 0 0-8 6 6 0 0 0-11.3-2.3A4.5 4.5 0 0 0 6 18z"/><path d="M9 14h6"/>',
    'custom'     => '<path d="M4 20h16"/><path d="M6 16 16 6l2 2L8 18H6z"/><path d="m14 8 2 2"/>',
  );
  return '<svg viewBox="0 0 24 24" aria-hidden="true">' . ($icons[$icon] ?? $icons['apps']) . '</svg>';
}
