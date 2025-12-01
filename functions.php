<?php
/**
 * Betheme Child Theme
 *
 * @package Betheme Child Theme
 * @author Muffin group
 * @link https://muffingroup.com
 */

/**
 * Load Textdomain
 */

function mfn_load_child_theme_textdomain()
{
    load_child_theme_textdomain('mfn-opts', get_stylesheet_directory() . '/languages');
    load_child_theme_textdomain('betheme', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'mfn_load_child_theme_textdomain');

/**
 * Enqueue Styles
 */

function mfnch_enqueue_styles()
{
    // enqueue the parent stylesheet
    // however we do not need this if it is empty
    // wp_enqueue_style('parent-style', get_template_directory_uri() .'/style.css');

    // enqueue the parent RTL stylesheet

    if (is_rtl()) {
        wp_enqueue_style('mfn-rtl', get_template_directory_uri() . '/rtl.css');
    }

    // enqueue the child stylesheet

    wp_dequeue_style('style');
    wp_enqueue_style('style', get_stylesheet_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'mfnch_enqueue_styles', 101);

/**
 * Enqueue Leaflet scripts and styles for OpenStreetMap
 */
function enqueue_leaflet_scripts()
{
    // Enqueue Leaflet CSS
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');

    // Enqueue Leaflet JS
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);

    // Enqueue custom map initialization script
    wp_enqueue_script('acf-map-init', get_stylesheet_directory_uri() . '/js/acf-map.js', array('leaflet-js'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_leaflet_scripts');

/**
 * Shortcode do wyświetlania galerii ACF zgodnie ze strukturą BeTheme
 * Użycie: [acf_galeria] lub [acf_galeria columns="3"]
 */
function acf_galeria_shortcode($atts)
{
    // Parametry shortcode
    $atts = shortcode_atts(array(
        'columns' => '3',
    ), $atts);

    // Pobierz pole galerii ACF
    $images = get_field('galeria');

    // Sprawdź czy galeria istnieje i zawiera obrazki
    if (!$images || !is_array($images)) {
        return '';
    }

    // Generuj unikalne ID dla galerii
    $gallery_id = 'acf_gallery_' . uniqid();
    $post_id = get_the_ID();
    $columns = intval($atts['columns']);
    $column_width = number_format(100 / $columns, 2, '.', '');

    // Rozpocznij budowanie HTML
    $output = '<div class="mcb-column-inner mfn-module-wrapper mcb-item-image_gallery-inner">';

    // Style dla galerii
    $output .= '<style type="text/css">
        #' . $gallery_id . ' {
            margin: auto;
        }
        #' . $gallery_id . ' .gallery-item {
            float: left;
            text-align: center;
            width: ' . $column_width . '%;
        }
        #' . $gallery_id . ' img {
            border: 2px solid #cfcfcf;
        }
    </style>';

    // Kontener galerii
    $output .= '<div id="' . $gallery_id . '" class="gallery galleryid-' . $post_id . ' gallery-columns-' . $columns . ' gallery-size-full gallery-default">';

    // Pętla przez obrazki
    foreach ($images as $image) {
        $image_url = isset($image['url']) ? esc_url($image['url']) : '';
        $image_alt = isset($image['alt']) ? esc_attr($image['alt']) : '';
        $image_title = isset($image['title']) ? esc_attr($image['title']) : '';
        $image_caption = isset($image['caption']) ? esc_attr($image['caption']) : '';
        $image_width = isset($image['width']) ? intval($image['width']) : '';
        $image_height = isset($image['height']) ? intval($image['height']) : '';

        // Generuj srcset zgodnie z WordPress
        $srcset = '';
        $srcset_array = array();

        if (!empty($image_url) && !empty($image_width)) {
            $srcset_array[] = $image_url . ' ' . $image_width . 'w';
        }

        // Dodaj wszystkie dostępne rozmiary
        if (isset($image['sizes'])) {
            $size_map = array(
                'thumbnail' => 'thumbnail-width',
                'medium' => 'medium-width',
                'medium_large' => 'medium_large-width',
                'large' => 'large-width',
                '1536x1536' => '1536x1536-width',
                '2048x2048' => '2048x2048-width'
            );

            foreach ($size_map as $size_name => $width_key) {
                if (isset($image['sizes'][$size_name]) && isset($image['sizes'][$width_key])) {
                    $srcset_array[] = esc_url($image['sizes'][$size_name]) . ' ' . intval($image['sizes'][$width_key]) . 'w';
                }
            }
        }

        if (!empty($srcset_array)) {
            $srcset = 'srcset="' . implode(', ', $srcset_array) . '"';
        }

        $output .= '<dl class="gallery-item" data-title="' . $image_title . '" data-description="' . $image_caption . '"><div class="gallery-item-wrapper">';
        $output .= '<dt class="gallery-icon landscape">';
        $output .= '<div class="image_frame scale-with-grid"><div class="image_wrapper">';
        $output .= '<a href="' . $image_url . '" rel="lightbox[' . $gallery_id . ']" data-elementor-lightbox-slideshow="' . $gallery_id . '" data-elementor-lightbox-title="' . $image_title . '" data-elementor-lightbox-description="' . $image_caption . '" data-lightbox-type="gallery">';
        $output .= '<div class="mask"></div>';
        $output .= '<img width="' . $image_width . '" height="' . $image_height . '" src="' . $image_url . '" class="attachment-full size-full" alt="' . $image_alt . '" decoding="async" ' . $srcset . ' sizes="(max-width:767px) 480px, (max-width:' . $image_width . 'px) 100vw, ' . $image_width . 'px">';
        $output .= '</a></div></div>';
        $output .= '</dt></div></dl>';
    }

    $output .= '</div>'; // gallery
    $output .= '</div>'; // mcb-column-inner

    return $output;
}
add_shortcode('acf_galeria', 'acf_galeria_shortcode');

/**
 * Shortcode do wyświetlania mapy OpenStreetMap na podstawie pól ACF
 * Użycie: [acf_mapa] lub [acf_mapa height="400px"]
 *
 * Wymagane pola ACF (typ: Text):
 * - lokalizacja_lat - szerokość geograficzna (latitude)
 * - lokalizacja_lng - długość geograficzna (longitude)
 * - lokalizacja_zoom - poziom przybliżenia (opcjonalne, domyślnie 15)
 * - lokalizacja_opis - opis lokalizacji (opcjonalne)
 */
function acf_mapa_shortcode($atts)
{
    // Parametry shortcode
    $atts = shortcode_atts(array(
        'height' => '450px',
        'width' => '100%',
        'zoom' => '', // Opcjonalne nadpisanie zoomu
    ), $atts);

    // Pobierz pola ACF
    $lat = get_field('lokalizacja_lat');
    $lng = get_field('lokalizacja_lng');
    $zoom_acf = get_field('lokalizacja_zoom');
    $opis = get_field('lokalizacja_opis');

    // Sprawdź czy współrzędne są ustawione
    if (empty($lat) || empty($lng)) {
        return '<div class="acf-map-error" style="padding:20px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:4px;">
            <strong>Brak współrzędnych mapy.</strong><br>
            Uzupełnij pola ACF: <code>lokalizacja_lat</code> i <code>lokalizacja_lng</code>
        </div>';
    }

    // Konwertuj na liczby
    $lat = floatval(str_replace(',', '.', $lat));
    $lng = floatval(str_replace(',', '.', $lng));

    // Walidacja współrzędnych
    if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
        return '<div class="acf-map-error" style="padding:20px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:4px;">
            <strong>Nieprawidłowe współrzędne.</strong><br>
            Szerokość geograficzna: -90 do 90<br>
            Długość geograficzna: -180 do 180
        </div>';
    }

    // Określ zoom
    if (!empty($atts['zoom'])) {
        $zoom = intval($atts['zoom']);
    } elseif (!empty($zoom_acf)) {
        $zoom = intval($zoom_acf);
    } else {
        $zoom = 15;
    }

    // Przygotuj opis dla popup
    if (!empty($opis)) {
        $popup_content = '<strong>' . esc_html($opis) . '</strong>';
    } else {
        $popup_content = '<strong>' . esc_html(get_the_title()) . '</strong>';
    }

    // Generuj unikalne ID dla mapy
    $map_id = 'acf_map_' . uniqid();

    // Przygotuj dane dla JavaScript
    $map_data = array(
        'lat' => $lat,
        'lng' => $lng,
        'zoom' => $zoom,
        'opis' => $popup_content,
    );

    // Rozpocznij budowanie HTML
    $output = '<div class="acf-map-wrapper" style="margin: 20px 0;">';
    $output .= '<div id="' . esc_attr($map_id) . '" class="acf-map" style="height: ' . esc_attr($atts['height']) . '; width: ' . esc_attr($atts['width']) . '; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" data-map=\'' . json_encode($map_data) . '\'></div>';
    $output .= '</div>';

    return $output;
}
add_shortcode('acf_mapa', 'acf_mapa_shortcode');

// generowanie slidera swiper.js dla slidera lokalizacji 
function my_slider_assets_clean()
{

    // Swiper CSS
    wp_enqueue_style(
        'swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        array(),
        null
    );

    // Własny CSS slidera (stwórz plik w swoim motywie: /assets/css/slider.css)
    wp_enqueue_style(
        'my-slider-css',
        get_stylesheet_directory_uri() . '/assets/css/slider.css',
        array(),
        null
    );

    // Swiper JS
    wp_enqueue_script(
        'swiper-js',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        array(),
        null,
        true
    );

    // Własny JS inicjalizujący slider (plik: /assets/js/slider-init.js)
    wp_enqueue_script(
        'my-slider-init',
        get_stylesheet_directory_uri() . '/js/slider-init.js',
        array('swiper-js'),
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'my_slider_assets_clean');