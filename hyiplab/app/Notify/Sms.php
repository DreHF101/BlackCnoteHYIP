<?php

namespace Blackcnotelab\Notify;

use Blackcnotelab\Notify\NotifyProcess;
use Blackcnotelab\Notify\SmsGateway;
use Blackcnotelab\Notify\Notifiable;


class Sms extends NotifyProcess implements Notifiable{

    /**
    * Mobile number of receiver
    *
    * @var string
    */
	public $mobile;

    /**
    * Assign value to properties
    *
    * @return void
    */
	public function __construct(){
		$this->statusField = 'sms_status';
		$this->body = 'sms_body';
		$this->globalTemplate = 'blackcnotelab_sms_body';
		$this->notifyConfig = 'sms_config';
		$this->gateway = new SmsGateway();
	}

    /**
    * Send notification
    *
    * @return void|bool
    */
	public function send($to, $message, $template = null){
		if ( get_option('blackcnotelab_sms_notification') && $message) {
			try {
                $sms_config = blackcnotelab_to_object( get_option('blackcnotelab_sms_config') );
				$sendSms = $this->gateway;
                if($to){
                    $sendSms->to = $to;
                    $sendSms->from = get_option('blackcnotelab_sms_from');
                    $sendSms->message = strip_tags($message);
                    $sendSms->config = $sms_config;
                    $sendSms->template = $template ?? $this->globalTemplate;
                    return $sendSms->send();
                }
			} catch (\Exception $e) {
				blackcnotelab_session()->flash('sms_error','API Error: '.$e->getMessage());
				return false;
			}
		}
		return false;
	}

    /**
    * Configure some properties
    *
    * @return void
    */
	public function prevConfiguration(){
		//Check If User
		if ($this->user) {
			$this->mobile = $this->user->mobile;
			$this->receiverName = $this->user->fullname;
		}
		$this->toAddress = $this->mobile;
	}
}
