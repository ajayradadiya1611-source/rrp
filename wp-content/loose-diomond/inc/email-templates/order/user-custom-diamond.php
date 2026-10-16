<?php
/**
 * User Custom Diamond Email
 *
 * Variables available:
 * @var string $order_number Order Number
 * @var string $first_name Customer's first name
 */

defined( 'ABSPATH' ) || exit;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Regarding Your Order #<?php echo esc_html($order_number); ?></title>
        <style>
            body{
                font-family: Arial, sans-serif;
                line-height:1.6;
                color:#333333;
                max-width:600px;
                margin:0 auto;
                padding:20px;
                background:#ffffff;
            }

            .email-container{
                background:#ffffff;
                border:1px solid #e0e0e0;
                border-radius:4px;
                padding:30px;
            }

            


            .footer{
                margin-top:40px;
                padding-top:20px;
                border-top:1px solid #e0e0e0;
                font-size:12px;
                color:#999999;
                text-align:center;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <p >
                Hello <?php echo esc_html($first_name); ?>,
            </p>

            <p>
                Congratulations on choosing your diamond &mdash; you've made a beautiful choice!
            </p>

            <p>
                I noticed that your order includes a loose diamond only. If you're still looking for the perfect setting, we'd be happy to create one custom-fit to your diamond.
            </p>

            <p>We offer:</p>

            <ul>
                <li>Complimentary CAD &amp; 3D design</li>
                <li>Custom-made settings tailored to your diamond</li>
                <li>Any style, metal, or inspiration design</li>
                <li>100% money-back guarantee</li>
            </ul>

            <p>
                Simply reply with the style you like, your ring size, and preferred metal &mdash; or just say <strong>"Suggest one for me,"</strong> and I'll be happy to help.
            </p>

            <div >
                <p>
                    Best regards,<br>
                    <strong>Team RRP Diamond</strong>
                </p>
            </div>

            <div class="footer">
                <p>
                    This email was sent regarding your recent order #<?php echo esc_html($order_number); ?>.<br>
                    You are receiving this email because you placed an order at <?php echo esc_html( get_bloginfo('name') ); ?>.
                </p>
            </div>
        </div>
    </body>
</html>