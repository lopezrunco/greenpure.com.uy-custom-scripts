<?php

// Cargar estilos del tema padre antes
function my_theme_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

//HEAD//OTROS///////////////////////////////////////////////////////////////////////////////////////////////////

// Modificar leyenda del administrador
add_filter('admin_footer_text', 'remove_footer_admin');
function remove_footer_admin () 
{
    echo '<span id="footer-thankyou"><a href="https://greenpure.com.uy/" target="_blank">Green Pure Uruguay</a>';
}

// Quitar logo de Wordpress del administrador
add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);
function annointed_admin_bar_remove() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
}

// Agregar shortcode breadcrumb yoast seo
add_shortcode( 'migas', 'breadcrumb_yoast' );
function breadcrumb_yoast( $atts ){
	yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
}

// Quitar proyectos de Divi
add_filter( 'et_project_posttype_args', 'mytheme_et_project_posttype_args', 10, 1 );
function mytheme_et_project_posttype_args( $args ) {
	return array_merge( $args, array(
		'public'              => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => false,
		'show_in_nav_menus'   => false,
		'show_ui'             => false
	));
}

//WOOCOMMERCE////////////////////////////////////////////////////////////////////////////////////////////////////

// Redirigir al checkout cuando se agrega al carrito
add_filter( 'woocommerce_add_to_cart_redirect', 'misha_skip_cart_redirect_checkout' );
function misha_skip_cart_redirect_checkout( $url ) {
    return wc_get_checkout_url();
}

// Agregar departamentos
add_filter( 'woocommerce_states', 'custom_woocommerce_states' );
function custom_woocommerce_states( $states ) {
  $states['UY'] = array(
    'UY01' => 'Artigas', 
    'UY02' => 'Canelones', 
    'UY03' => 'Cerro Largo', 
    'UY04' => 'Colonia', 
    'UY05' => 'Durazno', 
    'UY06' => 'Flores', 
    'UY07' => 'Florida', 
    'UY08' => 'Lavalleja', 
    'UY09' => 'Maldonado', 
    'UY10' => 'Montevideo', 
    'UY11' => 'Paysandú', 
    'UY12' => 'Río Negro', 
    'UY13' => 'Rivera', 
    'UY14' => 'Rocha', 
    'UY15' => 'Salto', 
    'UY16' => 'San José', 
    'UY17' => 'Soriano', 
    'UY18' => 'Tacuarembó', 
    'UY19' => 'Treinta y Tres'
  );
  return $states;
}

//WOOCOMMERCE//PRODCUTOS/////////////////////////////////////////////////////////////////////////////////////////

// Agregar botón de comprar a las listas de productos
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 20 );

// Cambiar el texto del botón añadir al carrito
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_add_to_cart_text' ); 
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_custom_add_to_cart_text' ); 
function woocommerce_custom_add_to_cart_text() {
	global $product;
	if( $product->get_price() ) {
		return 'Comprar';
	}
}

add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
function woo_rename_tabs( $tabs ) {
	$tabs['description']['title'] = __( 'Etapas de Filtración' );
	$tabs['description']['priority'] = 15;	
	$tabs['additional_information']['title'] = __( 'Información Técnica' );
	$tabs['additional_information']['priority'] = 10;
	return $tabs;
}

//WOOCOMMERCE//CHECKOUT/////////////////////////////////////////////////////////////////////////////////////////

// Agregar título a la sección de pago
add_filter( 'woocommerce_review_order_before_payment', 'agregar_titulo_pago', 5 );
function agregar_titulo_pago() {
	echo "<h3>Método de pago</h3>";
}

// Editar campos checkout
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
	
	// Desactivar
	unset($fields['shipping']['shipping_email']);

    return $fields;
}

// Editar campos de la dirección en billing
add_filter( 'woocommerce_billing_fields' , 'custom_override_billing_fields' );
function custom_override_billing_fields( $fields ) {
	
	// Desactivar
	
    // Modificar
    $fields['billing_email']['label'] = 'Email';
	
    return $fields;
}

// Editar campos de la dirección en shipping
add_filter( 'woocommerce_shipping_fields' , 'custom_override_shipping_fields' );
function custom_override_shipping_fields( $fields ) {
	
	// Desactivar
    unset($fields['shipping_company']);
    unset($fields['shipping_phone']);
    unset($fields['shipping_email']);
	
	// Modificar
    $fields['shipping_email']['label'] = 'Email';
	
    return $fields;
}

// Modificar campos de dirección
add_filter( 'woocommerce_default_address_fields' , 'override_default_address_fields' );
function override_default_address_fields( $address_fields ) {
	
	// Desactivar
    unset($address_fields['postcode']);
    
	// Cambiar texto
    $address_fields['address_1']['label'] = __('Dirección', 'woocommerce');
    $address_fields['address_1']['placeholder'] = __('Calle y número de puerta', 'woocommerce');
    $address_fields['address_1']['class'] = array('form-row-wide');
    $address_fields['city']['label'] = __('Ciudad/Localidad', 'woocommerce');
    $address_fields['city']['class'] = array('form-row-first');
    $address_fields['state']['label'] = __('Departamento', 'woocommerce');
    $address_fields['state']['class'] = array('form-row-last');
    $address_fields['email']['label'] = __('Email', 'woocommerce');
    $address_fields['email']['class'] = array('form-row-wide');
	
	// Reordenar
    $address_fields['first_name']['priority'] = 1;
    $address_fields['last_name']['priority'] = 2;
    $address_fields['company']['priority'] = 3;
    $address_fields['address_1']['priority'] = 4;
    $address_fields['address_2']['priority'] = 5;
    $address_fields['city']['priority'] = 6;
    $address_fields['state']['priority'] = 7;
    $address_fields['country']['priority'] = 8;
    $address_fields['phone']['priority'] = 9;
    $address_fields['email']['priority'] = 10;
	
    return $address_fields;
}

// Modificar el formato de la dirección
add_filter( 'woocommerce_localisation_address_formats', 'change_address_format' );
function change_address_format( $formats ) {
	$formats['UY'] = "{name}\n{company}\n{address_1}, {address_2}\n{city}, {state}";
	return $formats;
}

// COMIENZO CODIGO TECMEDIOS //////////////////////////////////////////////////////////////////////////////////////////

// Redireccionar a pagina de agradecimiento despues de enviar mensaje en Contact Form 7.
add_action( 'wp_footer', 'gp_cf7_redirect_script' );
function gp_cf7_redirect_script() {
    ?>
    <script>
        document.addEventListener('wpcf7mailsent', function(event) {
                setTimeout(function() {
                    window.location.href = 'https://greenpure.com.uy/gracias';
                }, false);
            // }
        }, false);
    </script>
    <?php
}

// Comprobar operacion matematica de formulario de Contact Form 7
add_filter('wpcf7_validate_text', 'cf7_math_validator', 10, 2);
add_filter('wpcf7_validate_text*', 'cf7_math_validator', 10, 2);

function cf7_math_validator($result, $tag) {
    if ($tag->name === 'math-answer') {
        if ( trim($_POST['math-answer']) != '8' ) {
            $result->invalidate($tag, "Respuesta incorrecta");
        }
    }
    return $result;
}

// Agregar mensaje personalizado en la categoria purificadores-sobre-mesada.

add_action( 'woocommerce_after_cart_item_name', 'show_free_product_message_per_category', 10, 2 );

function show_free_product_message_per_category( $cart_item, $cart_item_key ) {
    // Handle variations.
    $product_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

    $quantity = (int) $cart_item['quantity'];

    // Productos a los que aplica la promo:
    // Purificador Ceramic 4 Etapas.
    // Purificador Inox 4 Etapas.
    $allowed_product_ids = array (35, 36);

    if ( in_array( $product_id, $allowed_product_ids, true ) ) { 
        $message = ( $quantity === 1 ) ? '1 purificador de ducha de regalo.' : $quantity . ' purificadores de ducha de regalo.'; 
        $image_url = 'https://greenpure.com.uy/purificador-ducha-regalo.jpg';
        $free_price = '$0';
        
        echo '
            <div class="free-product-wrapper">
                <div class="free-product-image">
                    <img src="' . esc_url( $image_url ) . '" alt="Purificador de ducha de regalo" loading="lazy" />
                </div>

                <div class="free-product-content">
                    <p class="free-product-message">' . esc_html( $message ) . '</p>
                    <span class="free-product-price">' . esc_html( $free_price ) . '</span>
                </div>
            </div>
        ';
    }
}

add_action( 'wp_head', 'free_product_cart_styles' );

function free_product_cart_styles() {
    ?>
    <style>
        .free-product-wrapper {
            display: flex;
            gap: 10px;
            padding: 5px 10px;
            background: #88ba4224;
            border: 1px dashed #88ba42;
            border-radius: 6px;
            align-items: center;
        }

        .free-product-image img {
            width: 60px;
            height: auto;
            display: block;
        }

        .free-product-content {
            display: flex;
            flex-direction: row;
            gap: 10px;
        }

        .free-product-message {
            margin: 0;
            font-weight: 600;
            color: #1b5e20;
            font-size: 14px;
        }

        .free-product-price span {
            font-size: 14px;
            font-weight: bold;
            color: #2e7d32;
        }
    </style>
    <?php
}