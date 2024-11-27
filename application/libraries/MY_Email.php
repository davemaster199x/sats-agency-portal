<?php defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * This class was created to set a default from address on all emails but can be overwritten when needed
 * This is to reduce code duplication
 * @property Config $config
 */
class MY_Email extends CI_Email
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        // Everytime this class is created, set the from address and name defaults.
        // It can be overwritten in controllers if needed
        $this->from(
            make_email('info'),
            config_item('company_full_name')
        );

	    $this->bcc(make_email('bcc'));

        // Always sending HTML emails
        $this->set_mailtype("html");

        log_message('info', 'MY_Email Class Initialized - Default FROM Headers set & mailtype set to HTML');
    }



    /**
     * Set Email Subject
     * UTF 8 Subject line Override
     * https://stackoverflow.com/questions/52003599/email-subject-line-starts-with-utf-8q-in-codeigniter-project
     * @param	string $subject
     * @return	MY_Email
     */
    public function subject(string $subject): MY_Email
    {
        $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
        $this->set_header('Subject', $subject);
        return $this;
    }
}