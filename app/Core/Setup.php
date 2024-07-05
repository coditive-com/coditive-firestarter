<?php

namespace App\Core;

class Setup
{
    /**
     * @filter upload_mimes
     */
    public function setUploadTypes(array $mimes): array
    {
        if (! isset($mimes['svg'])) {
            $mimes['svg'] = 'image/svg+xml';
        }

        return $mimes;
    }

    /**
     * @action login_enqueue_scripts
     */
    public function setLoginLogo(): string
    {
        if (! empty($logo = firestarter()->settings()->get('site_logo'))) {
            ?>
                <style type="text/css">
                    #login h1 a,
                    .login h1 a {
                        background: url('<?php echo wp_get_attachment_image_url($logo, 'full'); ?>') no-repeat center center / 75%;
                        width: 100%;
                        height: 100px;
                        margin-top: 100px;
                    }

                    .login.interim-login #login h1 a {
                        margin-top: 0;
                    }
                </style>
            <?php
        }

        return '';
    }

    /**
     * @filter rest_authentication_errors
     */
    public function disableDefaultEndpoints(\WP_Error|bool|null $access): \WP_Error|bool|null
    {
        $endpointsToRemove = [
            '/wp/v2/users',
        ];

        if (! is_user_logged_in()) {
            $currentEndpoint = $GLOBALS['wp']->query_vars['rest_route'] ?: '';

            foreach ($endpointsToRemove as $toRemove) {
                if (false !== stripos($currentEndpoint, $toRemove)) {
                    if (is_wp_error($access)) {
                        $access->add(
                            'rest_forbidden',
                            __('Sorry, you are not allowed to do that.', 'firestarter'),
                            ['status' => rest_authorization_required_code()]
                        );
                    } else {
                        return new \WP_Error(
                            'rest_forbidden',
                            __('Sorry, you are not allowed to do that.', 'firestarter'),
                            ['status' => rest_authorization_required_code()]
                        );
                    }
                }
            }
        }

        return $access;
    }
}
