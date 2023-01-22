<?php
get_header();
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$checkout = WC()->checkout();
do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<form id="woo_co_form" name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>



		<div id="customer_details">
			<div class="wc_col col-1 active">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="wc_col col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
            <div class="wc_col col-3">
                <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
                
                <h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
                
                <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                <div id="order_review" class="woocommerce-checkout-review-order">
                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                </div>

                <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
            </div>
            <div id="steps-Control">
                <button id="prev" disabled>PREV</button>
                <button id="next">NEXT</button>
            </div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>
	
</form>
<style>
    .wc_col.active
    {
        display:block;
    }
    .wc_col
    {
        display:none;
    }

    #steps-control{
        display:flex;
        align-items:center;
    }
</style>
<script>
    jQuery(document).ready(function($){

        $('#next').on('click',function(e){
            e.preventDefault();
            $(this).parent().siblings('.wc_col.active').removeClass('active').next().addClass('active');

            if($('.wc_col.active').index() != 0){
                $("#prev").prop('disabled',false);
            } 

            if($('.wc_col.active').index() == $('.wc_col').length - 1){
                $("#next").prop('disabled',true);
            } 

        })
        $('#prev').on('click',function(e){
            e.preventDefault();
            $(this).parent().siblings('.wc_col.active').removeClass('active').prev().addClass('active');

            if($('.wc_col.active').index() == 0){
                $("#prev").prop('disabled',true);
            } 

            if($('.wc_col.active').index() != $('.wc_col').length - 1){
                $("#next").prop('disabled',false);
            } 
        })
        
    })
</script>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); 

get_footer();
?>
