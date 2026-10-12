<?php
// include DOMPDF autoloader
require_once get_template_directory() . '/includes/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

/**
 * Generate PDF for ALL languages when property is saved
 */
// function lsg_create_pdf_on_save($post_id)
// {
//     // Only property post type
//     if (get_post_type($post_id) !== 'property') {
//         return;
//     }

//     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
//         return;
//     }

//     // Get WPML languages
//     $languages = apply_filters('wpml_active_languages', null);

//     // Fallback if WPML inactive
//     if (!$languages) {
//         lsg_generate_single_pdf($post_id);
//         return;
//     }

//     foreach ($languages as $lang_code => $lang) {

//         // 🔥 Switch WPML language context
//         do_action('wpml_switch_language', $lang_code);
    
//         $translated_id = apply_filters(
//             'wpml_object_id',
//             $post_id,
//             'property',
//             false,
//             $lang_code
//         );
    
//         if (!$translated_id) {
//             continue;
//         }
    
//         lsg_generate_single_pdf($translated_id, $lang_code);
//     }
//     // Switch back to default language
//     do_action('wpml_switch_language', apply_filters('wpml_default_language', null));

    
// }
// add_action('save_post', 'lsg_create_pdf_on_save', 20);

/**
 * Generate single language PDF (DESIGN SAME)
 */
// function lsg_generate_single_pdf($post_id, $lang_code = 'en')
// {
//     $unbranded = create_pdf_document($post_id);
//     $branded   = create_pdf_document($post_id, true);

//     $title = strtolower(get_the_title($post_id));
//     $slug  = sanitize_title($title);

//     $path = WP_CONTENT_DIR . '/pdfs/new/';

//     if (!file_exists($path)) {
//         wp_mkdir_p($path);
//     }

//     // ✅ UNIQUE filenames (FIX)
//     file_put_contents(
//         $path . $slug . '-' . $post_id . '-' . $lang_code . '.pdf',
//         $unbranded
//     );

//     file_put_contents(
//         $path . $slug . '-' . $post_id . '-' . $lang_code . '-branded.pdf',
//         $branded
//     );
// }
	function lsg_generate_one_pdf($post_id, $lang, $branded = false)
	{
		do_action('wpml_switch_language', $lang);

		$pdf = create_pdf_document($post_id, $branded);

		$slug = sanitize_title(get_the_title($post_id));
		$path = WP_CONTENT_DIR . '/pdfs/new/';

		if (!file_exists($path)) {
			wp_mkdir_p($path);
		}

		$file = $slug . '-' . $post_id . '-' . $lang
			  . ($branded ? '-branded' : '')
			  . '.pdf';

		file_put_contents($path . $file, $pdf);

		return $file;
	}

/**
 * Create PDF document (UNCHANGED)
 */
function create_pdf_document($post_id, $branded = false)
{
    $dompdf = new Dompdf();

    $options = $dompdf->getOptions();
    $options->setIsRemoteEnabled(true);

    // 🔥 FIX-2: allow local filesystem access (SVG stability in bulk)
    $options->setChroot(ABSPATH);

    $dompdf->setOptions($options);

    $dompdf->loadHtml(pdf_template($post_id, $branded));
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 🔥 FIX-2: free memory between PDFs (critical for bulk)
    $output = $dompdf->output();
    unset($dompdf);
    gc_collect_cycles();

    return $output;
}

/**
 * PDF TEMPLATE (100% SAME DESIGN)
 */
function pdf_template($post_id, $branded = false)
{
    // Get image overlay for branded PDFs
    $image_overlay = '';

    $overlay_id = get_field('image_overlay', 'option');
    if ($branded && $overlay_id) {
        $overlay_path = get_attached_file($overlay_id);
        $image_overlay = '<img class="image-overlay" src="' . $overlay_path . '" />';
    }

    $title = get_the_title($post_id);
    $subtitle = get_field('subtitle', $post_id);
    $property_id = get_field('property_id', $post_id);
    $bedrooms = get_field('bedrooms', $post_id);
    $bathrooms = get_field('bathrooms', $post_id);
    $price = get_field('property_price', $post_id);
    $internal_size = get_field('internal_size')['size'] ?? '';
    $plot_size = get_field('plot_size')['size'] ?? '';

    // Property status
    $terms = get_the_terms($post_id, 'property_status');
    $for_sale = isset($terms[0]) && in_array($terms[0]->slug, ['for-sale', 'for-sale-es']);

    $for_sale_html = '';
    $for_sale_width = '40%';

    if ($for_sale) {
        $for_sale_width = '50%';
        $for_sale_html = "<div class='seperator'></div>
            <div>
                <h3>$price &euro;</h3>
            </div>";
    }

    // Header image
    $pdf_header_image = get_template_directory() . '/assets/images/header.png';

    // Content
    $post = get_post($post_id);
    $content = apply_filters('the_content', $post->post_content);

    // Icons
    $house_icon = get_template_directory() . '/assets/images/house-solid-black.svg';
    $tree_icon = get_template_directory() . '/assets/images/tree-solid-black.svg';
    $bed_icon = get_template_directory() . '/assets/images/bed-solid-black.svg';
    $sink_icon = get_template_directory() . '/assets/images/sink-solid-black.svg';
    $chevron_icon = get_template_directory() . '/assets/images/chevron-right-solid.svg';

    // Featured image
    $featured_image = get_attached_file(get_post_thumbnail_id($post_id));


    // Gallery
    $gallery_html = '';
    $gallery = get_field('gallery', $post_id);
    if ($gallery && is_array($gallery)) {
        $gallery_images = array_chunk($gallery, 6);
        foreach ($gallery_images as $images) {
            $gallery_html .= '<div class="page-break"></div><div class="images">';
            foreach ($images as $image) {
                $img_path = get_attached_file($image['ID']);
                if (!$img_path) {
                    continue;
                }
                $gallery_html .= '<div class="image">' .
                    $image_overlay .
                    '<img src="' . esc_url($img_path) . '" />' .
                    '</div>';
            }
            $gallery_html .= '</div>';
        }
    }

     // Header
     $header_html = '';
     $header_css = '.featured-image {
         margin-top: -1cm;
     }';
     if ($branded) {
         $header_html = '<header><img src="' . $pdf_header_image . '"></header>';
         $header_css = '';
     }

    /**
     * Create PDF template
     */
    $template = <<<TEMPLATE
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>$post_id</title>
                <style>
                    @import url("https://use.typekit.net/vml6itj.css");

                    @page {
                        margin: 1cm;
                    }
                    
                    html,
                    body {
                        font-size: 11px;
                        font-family: gill-sans-nova, sans-serif;
                        font-weight: lighter;
                    }

                    .featured-image {
                        position: relative;
                        width: 112%;
                        margin-left: -6%;
                        height: 375px;
                        background-image: url("file://$featured_image");
                        background-size: cover;
                        background-position: center;
                    }

                    .featured-image .image-overlay {
                        width: 25%;
                    }

                    h1,
                    h2 {
                        text-align: center;
                    }

                    p,
                    li {
                        margin-top: 5px;
                    }

                    h2 {
                        font-weight: bold;
                    }

                    ul {
                        margin-top: 10px;
                    }

                    ul li {
                        display: inline-block;
                        width: 33%;
                    }

                    header {
                        position: absolute;
                        top: -1cm;
                        left: -1cm;
                        right: -1cm;
                        height: 1cm;
                        z-index: 10;
                    }

                    $header_css

                    header img {
                        width: 100%;
                    }

                    .heading {
                        text-transform: uppercase;
                    }

                    .heading h2 {
                        margin-top: -5px;
                        color: black;
                        font-weight: lighter;
                    }

                    .heading-details {
                        width: $for_sale_width;
                        margin: auto;
                        margin-top: -15px;
                        color: black;
                    }

                    .heading-details h3 {
                        font-weight: lighter;
                        color: black;
                    }

                    .heading-details div {
                        float: left;
                    }

                    .heading-details div.seperator {
                        width: 1px;
                        height: 20px;
                        background-color: #D7C8A8;
                        margin-top: 10px;
                        margin-left: 10px;
                        margin-right: 10px;
                    }

                    img {
                        max-width: 100%;
                        height: auto;
                    }

                    .details {
                        background-color: #D7C8A8;
                        color: white;
                        padding: 10px 0;
                        width: 112%;
                        margin-left: -6%;
                    }

                    .details__inner {
                        width: 50%;
                        margin: auto;
                    }

                    .detail {
                        float: left;
                        text-align: center;
                        margin: auto;
                        width: 25%;
                    }

                    .detail p {
                        margin-bottom: 0;
                    }

                    .detail img {
                        height: 20px;
                        width: auto;
                        margin-bottom: 5px;
                    }

                    li img {
                        width: 5px;
                        margin-right: 10px;
                    }

                    footer {
                        position: fixed; 
                        bottom: 0cm; 
                        left: 0cm; 
                        right: 0cm;
                        height: 1cm;
                        font-size: 10px;
                    }

                    .hide-image {
                        opacity: 0;
                    }

                    .page-break {
                        page-break-after: always;
                    }

                    .clear-both {
                        clear: both;
                        color: black;
                    }

                    .features {
                        margin-top: 20px;
                    }

                    .gallery {
                        margin: 1cm !important;
                    }

                    .gallery .images {
                        padding-top: 1cm;
                    }

                    .gallery .image {
                        position: relative;
                        display: inline-block;
                        width: 48%;
                        height: 200px;
                        margin: 10px 1%;
                    }

                    .image-overlay {
                        position: absolute;
                        width: 50%;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        opacity: .5;
                    }
                </style>
            </head>

            <body>
                $header_html
                <footer>
                    <small>Sale price does not include costs or taxes. Additional costs for the buyer: inscription and notary fees, ITP 7% or, alternatively 10% VAT and AJD (1.2% on the purchase price) on new
                    properties and subject to some requisites to be met. This info is subject to errors, omissions, modifications, prior sale or withdrawal from the market. Information sheet available, Decree
                    218/2005 Oct. 11th. Rental prices, conditions and availability are believed to be correct, however are subject to modifications by owners at short notice. All details will be reconfirmed upon
                    receiving the booking request.</small>
                </footer>
                <div class="clear-both featured-image">$image_overlay</div>
                <div class="details">
                    <div class="details__inner">
                        <div class="detail">
                            <img style="fill: black;stroke: black;filter: brightness(0) saturate(100%) invert(0%) sepia(2%) saturate(0%) hue-rotate(192deg) brightness(99%) contrast(102%);" src="$house_icon">
                            <p style="color: black">$internal_size M<sup>2</sup></p>
                        </div>
                        <div class="detail">
                            <img style="fill: black;stroke: black;filter: brightness(0) saturate(100%) invert(0%) sepia(2%) saturate(0%) hue-rotate(192deg) brightness(99%) contrast(102%);" src="$tree_icon">
                            <p style="color: black">$plot_size M<sup>2</sup></p>
                        </div>
                        <div class="detail">
                            <img style="fill: black;stroke: black;filter: brightness(0) saturate(100%) invert(0%) sepia(2%) saturate(0%) hue-rotate(192deg) brightness(99%) contrast(102%);" src="$bed_icon">
                            <p style="color: black">$bedrooms Bedrooms</p>
                        </div>
                        <div class="detail">
                            <img style="fill: black;stroke: black;filter: brightness(0) saturate(100%) invert(0%) sepia(2%) saturate(0%) hue-rotate(192deg) brightness(99%) contrast(102%);" src="$sink_icon">
                            <p style="color: black">$bathrooms Bathrooms</p>
                        </div>
                        <div class="clear-both"></div>
                    </div>
                </div>
                <div class="heading">
                    <h1 >$title</h1>
                    <h2 style="color: black !important;">$subtitle</h2>
                    <div class="heading-details">
                        <div>
                            <h3 style="color: black !important;">REF: $property_id</h3>
                        </div>
                        
                        <div class="seperator"></div>
                        <div>
                            <h3 style="color: black !important;">$bedrooms BEDROOMS</h3>
                        </div>
                        
                        <div class="seperator"></div>
                        <div>
                            <h3 style="color: black !important;">$bathrooms BATHROOMS</h3>
                        </div>
                        $for_sale_html
                    </div>
                </div>
                <main class="clear-both" style="color: black">

                    <h3>Description</h3>
                    <div>
                    $content
                    </div>
             

                </main>
                <div class="gallery">$gallery_html</div>
            </body>
        </html>
    TEMPLATE;

    // <h3 class="features">Features</h3>
    // $features_html

    return $template;
}