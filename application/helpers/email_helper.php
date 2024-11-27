<?php

if ( ! function_exists('make_email'))
{
    /**
     * This was created to make code more readable, a simple function to do a simple task
     * This will add the sites domain to the email username specified so that it works on multiple environments
     * @param string $email_username
     * @return string
     */
    function make_email(string $email_username = '')
    {
        $CI =& get_instance();

        if(empty($email_username)){
            $email_username = 'noreply';
        }

        $domain = $CI->config->item('base_domain');

        return $email_username . '@' . $domain;
    }
}