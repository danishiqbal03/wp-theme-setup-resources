<?php
// Add custom field for cover image
function is_image_or_video_url( $url ) {
    $filetype = wp_check_filetype( $url );
    $image_types = array( 'jpg', 'jpeg', 'png', 'gif' );
    $video_types = array( 'mp4', 'm4v', 'webm', 'ogv', 'wmv' );

    if ( in_array( $filetype['ext'], $image_types ) ) {
        return 'image';
    } elseif ( in_array( $filetype['ext'], $video_types ) ) {
        return 'video';
    } else {
        return 'unknown';
    }
}
function add_banner_slider_meta_box() {
    $post_id = isset( $_GET['post'] ) ? $_GET['post'] : null;
    if ( $post_id && $post_id == 7 ) {
        add_meta_box(
            'banner-slider',
            __( 'Banner Slider', 'textdomain' ),
            'banner_slider_callback',
            'page',
            'normal',
            'default'
        );
    }

}
add_action( 'add_meta_boxes', 'add_banner_slider_meta_box' );

function banner_slider_callback(){
    wp_nonce_field( basename( __FILE__ ), 'banner_slider_nonce' );
    $banner_slider = get_post_meta( $post->ID, '_banner_slider', true );
    ?>
        <div id="bs_container">
            <?php
            if(!empty($banner_slider)){
                foreach($banner_slider as $k_bs => $v_bs){
            ?>
                <div id="bs_div_<?php echo $k_bs; ?>" class="bs_div">
                    <div id='bs_img_cont_<?php echo $k_bs; ?>' class="bs_img_cont">
                        <input type="hidden" name="bs_img_inp[]" id="bs_img_inp_<?php echo $k_bs; ?>" value="<?php echo $v_bs['img']; ?>" class="bs_img_inp">
                        <img src="<?php echo $v_bs['img']; ?>" alt="" id="bs_img_prev_<?php echo $k_bs; ?>" class="bs_img_prev">
                        <input type="button" class="button button-secondary upload-slider-media" value="<?php _e( 'Upload Slider Image', 'textdomain' ); ?>" />
                    </div>
                    <div id='bs_info_<?php echo $k_bs; ?>' class="bs_info">
                        <div id="bs_heading_cont_<?php echo $k_bs; ?>" class="bs_heading_cont">
                            <label for="bs_heading">Heading</label>
                            <input type="text" name='bs_heading[]' id='bs_heading_<?php echo $k_bs; ?>' class="bs_heading" value="<?php echo $v_bs['heading']; ?>">
                        </div>
                        <div id="bs_desc_cont_<?php echo $k_bs; ?>" class="bs_desc_cont">
                            <label for="bs_desc">Description</label>
                            <textarea name="bs_desc[]" id="bs_desc_<?php echo $k_bs; ?>" cols="30" rows="10" class='bs_desc'><?php echo $v_bs['desc']; ?></textarea>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
            ?>

                <div id="bs_div_0" class="bs_div">
                    <div id='bs_img_cont_0' class="bs_img_cont">
                        <div class="bs_img_div">
                            <img src="" alt="" id="bs_img_prev_0" class="bs_img_prev" style="max-width: 220px;width:100%" />
                            <p class="remove-banner-media" style="cursor:pointer;color:#2271b1;">Remove Media</p>
                        </div>
                        <div class="bs_img_btn_div">
                            <label for="bs_img_inp">Upload Image</label>
                            <input type="hidden" name="bs_inp[0]['url']" id="bs_img_inp_0" class="bs_img_inp">
                            <input type="button" class="button button-secondary upload-slider-media" value="<?php _e( 'Upload Slider Image', 'textdomain' ); ?>" />
                        </div>
                    </div>
                    <div id='bs_info_0' class="bs_info">
                        <div id="bs_heading_cont_0" class="bs_heading_cont">
                            <label for="bs_heading">Heading</label>
                            <input type="text" name="bs_inp[0]['heading']" id='bs_heading_0' class="bs_heading">
                        </div>
                        <div id="bs_desc_cont_0" class="bs_desc_cont">
                            <label for="bs_desc">Description</label>
                            <textarea name="bs_inp[0]['desc']" id="bs_desc_0" cols="30" rows="10" class='bs_desc'></textarea>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </div>
        <button id="add_more_slider" class="button">&plus;</button>
    <style>
        .bs_div{
            display: grid;
            grid-template-columns: 250px auto;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
            border-radius: 10px;
        }
        .bs_info>div{
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .bs_img_cont .bs_img_btn_div{
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }
        .bs_img_cont{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .bs_info>div label,
        .bs_img_cont label{
            font-weight:600;
            margin-bottom: 5px;
        }
        .bs_info>div>*{
            width:100%;
        }
        .remove-banner-media{
            text-align: center;
        }
        .bs_img_div{
            display: none;
        }
        #banner-slider .inside{
            display: flex;
            flex-direction: column;
        }
        #add_more_slider{
            margin: 0 auto;
        }
    </style>
    <script>
        jQuery(document).ready(function($){
            $(document).on('click','.remove-banner-media',function(){
                console.log($(this));
                $(this).parent().siblings('.bs_img_btn_div').find('.bs_img_inp').val('');
                $(this).siblings('.bs_img_prev').attr('src','');
                $(this).parent().hide().siblings().show();
            }); 
            var mediaUploader;
            $(document).on('click','.upload-slider-media',function(e) {
                e.preventDefault();
                var crr_btn = $(this);
                mediaUploader = wp.media({
                    title: '<?php _e( 'Choose Banner Image', 'textdomain' ); ?>',
                    button: {
                        text: '<?php _e( 'Choose Image', 'textdomain' ); ?>'
                    },
                    multiple: false,
                    library: {
                        type: [ 'image']
                    },
                });
                mediaUploader.on('select', function() {
                    attachment = mediaUploader.state().get('selection').first().toJSON();
                    console.log(attachment.url);
                    if(attachment.type == 'image'){
                        console.log("The attachment is ",attachment);
                        $('#banner-media-inp').val('');

                        console.log("The closest ", crr_btn.closest('.bs_img_cont'));
                        crr_btn.closest('.bs_img_cont').find('.bs_img_div').find('.bs_img_prev').attr('src',attachment.url);
                        crr_btn.parent().siblings('.bs_img_div').show();
                    }
                    crr_btn.parent().find('.bs_img_inp').val(attachment.url);
                    crr_btn.parent().hide();

                });
                mediaUploader.open();
            });

            $("#add_more_slider").on('click',function(e){
                e.preventDefault();
                let div_id = $('#bs_container>.bs_div').length;
                let div_html = `
                <div id="bs_div_${div_id}" class="bs_div">
                    <div id='bs_img_cont_${div_id}' class="bs_img_cont">
                        <div class="bs_img_div">
                            <img src="" alt="" id="bs_img_prev_${div_id}" class="bs_img_prev" style="max-width: 220px;width:100%" />
                            <p class="remove-banner-media" style="cursor:pointer;color:#2271b1;">Remove Media</p>
                        </div>
                        <div class="bs_img_btn_div">
                            <label for="bs_img_inp">Upload Image</label>
                            <input type="hidden" name="bs_inp[${div_id}]['url']" id="bs_img_inp_${div_id}" class="bs_img_inp">
                            <input type="button" class="button button-secondary upload-slider-media" value="<?php _e( 'Upload Slider Image', 'textdomain' ); ?>" />
                        </div>
                    </div>
                    <div id='bs_info_${div_id}' class="bs_info">
                        <div id="bs_heading_cont_${div_id}" class="bs_heading_cont">
                            <label for="bs_heading">Heading</label>
                            <input type="text" name="bs_inp[${div_id}]['heading']" id='bs_heading_${div_id}' class="bs_heading">
                        </div>
                        <div id="bs_desc_cont_${div_id}" class="bs_desc_cont">
                            <label for="bs_desc">Description</label>
                            <textarea name="bs_inp[${div_id}]['desc']" id="bs_desc_${div_id}" cols="30" rows="10" class='bs_desc'></textarea>
                        </div>
                    </div>
                </div>
                `;
                $('#bs_container').append(div_html);
            })
        })
    </script>
    <?php
}


function save_banner_slider_meta( $post_id ) {
    if ( ! isset( $_POST['banner_slider_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['banner_slider_nonce'], basename( __FILE__ ) ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    $banner_slider= isset($_POST['bs_inp']) ? $_POST['bs_inp'] : '';


    update_post_meta( $post_id, '_banner_slider', $banner_slider );
}
add_action( 'save_post', 'save_banner_slider_meta' );
