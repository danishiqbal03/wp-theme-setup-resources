<?php

function add_banner_media_meta_box() {
    add_meta_box(
        'banner-media',
        __( 'Banner Media', 'textdomain' ),
        'banner_media_callback',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_banner_media_meta_box' );

function banner_media_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'banner_media_nonce' );
    $banner_media = get_post_meta( $post->ID, '_banner_media', true );
    ?>
    <p>
        <label for="banner-media"><?php _e( 'Upload Banner Media', 'textdomain' ); ?></label>
        <br />
        <input type="text" name="banner-media-inp" id="banner-media-inp" class="regular-text" style='width:fit-content; margin:5px 0px;' value="<?php echo esc_attr( $banner_media ); ?>"/>
        <input type="button" class="button button-secondary" value="<?php _e( 'Upload Media', 'textdomain' ); ?>" id="upload-banner-media" />
    </p>
    <p class='media_prev'>
        <!-- <img src="<?php //echo esc_attr( $banner_media ); ?>" alt="" id="banner-media-preview" style="max-width: 100%;" /> -->
        <?php 
            if($banner_media){
                $check_type = is_image_or_video_url($banner_media);
                if($check_type == 'video'){
                    ?>
                        <video id="banner-media-preview" style="max-width: 100%;" controls>
                            <source src="<?php echo esc_attr( $banner_media ); ?>" type="video/<?php echo wp_check_filetype( $banner_media )['ext'];?>">
                        </video>
                    <?php
                } else if($check_type == 'image'){
                    ?>
                        <img src="<?php echo esc_attr( $banner_media ); ?>" alt="" id="banner-media-preview" style="max-width: 100%;" />
                    <?php
                }
                ?>
                <p id="remove-banner-media" style="cursor:pointer;color:#2271b1;">Remove Media</p>
                <?php
            }
        ?>

    </p>
    <script>
        jQuery(document).ready(function($){
            $(document).on('click','#remove-banner-media',function(){
                $('#banner-media-preview').remove();
                $('#banner-media-inp').val('');
                $('#remove-banner-media').remove();
            });

            var mediaUploader;
            $('#upload-banner-media').click(function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: '<?php _e( 'Choose Banner Media', 'textdomain' ); ?>',
                    button: {
                        text: '<?php _e( 'Choose Media', 'textdomain' ); ?>'
                    },
                    multiple: false,
                    library: {
                        type: [ 'image','video' ]
                    },
                });
                mediaUploader.on('select', function() {
                    attachment = mediaUploader.state().get('selection').first().toJSON();
                    console.log(attachment.url);
                    if(attachment.type == 'video'){
                        $('#banner-media-inp').val('');
                        var extension = attachment.url.split('.').pop();
                        $('.media_prev').html('<video id="banner-media-preview" style="max-width: 100%;" controls><source src="'+attachment.url+'" type="video/'+extension+'"></video><p id="remove-banner-media" style="cursor:pointer;color:#2271b1;">Remove Media</p>');
                    } else if(attachment.type == 'image'){
                        $('#banner-media-inp').val('');
                        $('.media_prev').html('<img src="'+attachment.url+'" alt="" id="banner-media-preview" style="max-width: 100%;" /><p id="remove-banner-media" style="cursor:pointer;color:#2271b1;">Remove Media</p>');
                    }
                    $('#banner-media-inp').val(attachment.url);
                    $('#banner-media-inp').prop('readonly','readonly');
                    
                });
                mediaUploader.open();
            });
        });
    </script>
    <?php
}

function save_banner_media_meta( $post_id ) {
    if ( ! isset( $_POST['banner_media_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['banner_media_nonce'], basename( __FILE__ ) ) ) {
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

    $banner_media = sanitize_text_field( $_POST['banner-media-inp'] );
    update_post_meta( $post_id, '_banner_media', $banner_media );
}
add_action( 'save_post', 'save_banner_media_meta' );
