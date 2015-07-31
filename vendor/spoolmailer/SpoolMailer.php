<?php
namespace vendor\spoolmailer;

use Swift_Mailer;
use Yii;



class SpoolMailer {

    public $mailer;
    public $spool;

    public function __construct()
    {


        $path = realpath(dirname(__FILE__)) . "/../runtime/mail";

        $this->mailer = new Swift_Mailer(new \Swift_Transport_SpoolTransport(
            new \Swift_Events_SimpleEventDispatcher(),
            new \Swift_FileSpool($path)
        ));

        $this->spool = $this->mailer->getTransport()->getSpool();

        if ($this->spool instanceof \Swift_FileSpool) {
            $this->spool->recover();
        }

    }

    function spoolMailerParam($name, $default = null)
    {
        if ( isset(Yii::$app->spoolmailer->$name) )
            return Yii::$app->spoolmailer->$name;
        else
            return $default;
    }



    public function newMail($from, $to, $subject, $text)
    {

        $message = \Swift_Message::newInstance()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->addPart($text, 'text/html')
            ->setBody($text);
        $this->spool->queueMessage($message);

    }

    public function send()
    {

        $smtpTransport = new \Swift_SmtpTransport(
            $this->spoolMailerParam('host'),
            $this->spoolMailerParam('port')
        );

        $smtpTransport->setUsername($this->spoolMailerParam('username'));
        $smtpTransport->setPassword($this->spoolMailerParam('password'));

        $this->spool->flushQueue($smtpTransport);
    }







}