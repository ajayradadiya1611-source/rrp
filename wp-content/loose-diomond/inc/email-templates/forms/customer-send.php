<?php
defined('ABSPATH') || exit;

$site_name = get_bloginfo('name');
$logo_id   = get_theme_mod('custom_logo');
$logo_url  = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($email_heading); ?></title>
</head>

<body style="background-color:#f7f7f7;margin:0;padding:0;text-align:center;">

<table width="100%" style="background-color:#f7f7f7;">
<tr>
<td></td>

<td width="800">
<div style="max-width:800px;margin:0 auto;padding:24px 0;">

<table width="100%" style="background:#ffffff;border-radius:8px;width: 100%;">

    <!-- HEADER LOGO -->
    <tr>
        <td style="padding:32px 32px 0;text-align:center;">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/image/logo/loosegrowndiamond_logo.svg' ); ?>" 
                     alt="<?php echo esc_attr( $site_name ); ?>"
                     style="max-width:120px;height:auto;display:block;margin:0 auto;">
            </td>
    </tr>

    
    <!-- DIVIDER -->
    <tr>
        <td>
            <hr style="border:none;height:6px;background:#f7f7f7;">
        </td>
    </tr>

    <!-- BODY (DYNAMIC CONTENT) -->
    <tr>
        <td style="padding:20px 32px 32px;text-align:left;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;">
            
            <?php echo wp_kses_post($html_content); ?>

        </td>
    </tr>


</table>

<!-- FOOTER -->
<table width="100%" style="background:#ffffff;border-radius:8px;width: 100%;">
<tr>
<td style="padding:32px;text-align:center;font-size:12px;font-family:Arial,Helvetica,sans-serif;border-top:1px solid rgba(0,0,0,0.1);margin-b">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:15px auto 0;">
        <tr>
            
            <td style="padding:0 6px;">
                <a href="https://twitter.com/grown_diamond" target="_blank">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/aj_custom/email-social/twitter.png' ); ?>" 
                         alt="Twitter" width="24" style="display:block;border:0;">
                </a>
            </td>

            <td style="padding:0 6px;">
                <a href="https://www.facebook.com/LooseGrownDiamond" target="_blank">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/aj_custom/email-social/facebook.png' ); ?>" 
                         alt="Facebook" width="24" style="display:block;border:0;">
                </a>
            </td>

            <td style="padding:0 6px;">
                <a href="https://www.instagram.com/loosegrown_diamond/" target="_blank">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/aj_custom/email-social/insta.png' ); ?>" 
                         alt="Instagram" width="24" style="display:block;border:0;">
                </a>
            </td>

            <td style="padding:0 6px;">
                <a href="https://www.youtube.com/channel/UCwlJvdgFUqw2wUyW5rAGxmw" target="_blank">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/aj_custom/email-social/youtube.png' ); ?>" 
                         alt="Pinterest" width="24" style="display:block;border:0;">
                </a>
            </td>
            <td style="padding:0 6px;">
                <a href="https://in.pinterest.com/LooseGrownDiamond" target="_blank">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/aj_custom/email-social/pinterest.png' ); ?>" 
                         alt="Pinterest" width="24" style="display:block;border:0;">
                </a>
            </td>

        </tr>
    </table>
    <p style="margin-bottom:0;margin-top:20px;">
        &copy; <?php echo date('Y'); ?> <?php echo esc_html($site_name); ?>.
    </p>

    <p style="margin:8px 0 0;">
        <?php echo  site_url();?>
    </p>

</td>
</tr>
</table>

</div>
</td>

<td></td>
</tr>
</table>

</body>
</html>