<?php
    $ulisting_default_placeholder = get_post(get_option("ulisting_default_placeholder"));
    $socials = get_option(\uListing\Admin\Classes\StmEmailTemplateManager::SOCIAL_OPTION);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_bloginfo( 'name' );?></title>
    <style>
        .email-btn {transition: background-color .2s linear}
        .email-btn:hover{background-color:#153643 !important}
    </style>

</head>
<body style="margin:0;padding:0;background:#f6f9fc;">
<center class="wrapper" style="width:100%;padding:40px 0;table-layout:fixed;background:#f6f9fc;">
    <div class="webkit" style="max-width:600px;background:#fff;">
        <table class="outer" align="center" style="margin:0 auto;width:100%;max-width:600px;border-spacing:0;font-family:sans-serif;color:#4a4a4a;">
            <?php if ( isset($header['url']) && !empty($header['url']) && isset($header['status']) && $header['status']):?>
            <tr bgcolor="#f6f9fc">
                <td style="padding:0;">
                    <a href="#" style="text-decoration:none;color:#388CDA;font-size:16px;"><img src="<?php echo esc_url($header['url'])?>" alt="Banner"/></a>
                </td>
            </tr>
            <?php endif;?>
            <tr>
                <td style="padding:0;">
                    <table width="100%" style="border-spacing:0;border-spacing: 0;">
                        <tr>
                            <td style="padding:0;background-color: <?php echo esc_attr($color);?> ; padding: 10px; text-align: center;">
                                <p style="color: #fff"><?php echo apply_filters('sanitize-email-subject', $subject);?></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php if ( isset($banner['url']) && !empty($banner['url']) && isset($banner['status']) && $banner['status']):?>
            <tr>
                <td style="padding:0;">
                    <a href="#" style="text-decoration:none;color:<?php echo esc_attr($color);?>;font-size:16px;"><img src="<?php echo esc_url($banner['url'])?>" alt="Banner" style="border:0;max-width: 100%;" width="600"/></a>
                </td>
            </tr>
            <?php endif;?>
            <tr>
                <td style="padding:0;">
                    <table width="100%" style="border-spacing:0 ;border-spacing: 0; padding: 20px;">
                        <tr>
                            <td>
                                <?php echo apply_filters('sanitize-email-content', $content);?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php if (isset($footer) && $footer):?>
            <tr>
                <td style="padding:0;">
                    <table width="100%" style="border-spacing:0;border-spacing: 0;">
                        <tr>
                            <td style="padding:0 0 10px 0;background-color: <?php echo esc_attr($color);?>;  text-align: center;">
                                <p style="font-size: 18px; color: #fff; margin-bottom: 13px;">
                                    <?php echo __('Connect with us', 'ulisting')?>
                                </p>
                                <?php foreach ($socials as $social):?>
                                    <?php if (!empty($social['link'])):?>
                                        <a href="<?php echo esc_url($social['link'])?>" style="text-decoration:none;color:<?php echo esc_attr($color);?>;font-size:16px;">
                                            <img src="<?php echo esc_url( ULISTING_URL . '/assets/img/socials/' . strtolower($social['label']) . '.png' )?>" alt="<?php echo esc_attr($social['label'])?>" width="30" style="border:0;">
                                        </a>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php endif;?>
        </table>
    </div>
</center>
</body>
</html>