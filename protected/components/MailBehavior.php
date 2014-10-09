<?php
/**
 * MailBehavior class file.
 * @author Vincent Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; MIAMA 2014
 * @package app.components
 */

/**
 * Mail module behavior for the Yii Application object.
 *
 * @property CWebApplication $owner The application object.
 */
class MailBehavior extends CBehavior
{
    /**
     * @var YiiMailer object.
     */
    private $_mail;

    /**
     * Send mail method
     * Valid arguments:
     * $mail->sendMail('', 'john@example.com', 'sub', 'msg', data[], 'view');
     * $mail->sendMail(array('name'=>'John Doe',
     *                       'email'=>'john@example.com'),
     *                 array('john@example.com',
     *                       'jane@example.com'),
     *                 'sub', 'msg', data[], 'view');
     * $mail->sendMail('john@example.com',
     *                 array('john@example.com'=>'John Doe',
     *                       'jane@example.com'),
     *                 'sub', 'msg', data[], 'view');
     * @param mixed $from address of the recipient.
     * @param mixed $to address of the recipient(s).
     * @param string $subject subject of the message.
     * @param string $message content of the message.
     * @param array $data data that will be passed to the view.
     * @param string $view the view to use, defaults to contact.
     * @return mixed true if successful or the error message received.
     */
    public function sendMail($from, $to, $subject, $message, $data = array(), $view = 'contact')
    {
        if(!isset($this->_mail)) {
            $this->getMailer();
        }

        $message = wordwrap($message, 70);
        $message = str_replace("\n.", "\n..", $message);

        //set properties
        $data['to'] = $to;
        $data['subject'] = $subject;
        $data['message'] = $message;
        $this->_mail->setView($view);

        if($from == '') {
            $this->_mail->setFrom(Yii::app()->params['fromEmail']['email'], Yii::app()->params['fromEmail']['name']);
            $this->_mail->setReplyTo(Yii::app()->params['replyEmail']['email'], Yii::app()->params['replyEmail']['name']);
            $data['from'] = Yii::app()->params['adminEmail'];
            $this->_mail->setData($data);
        } elseif(is_array($from)) {
            $this->_mail->setFrom($from['email'], $from['name']);
            $this->_mail->setReplyTo($from['email'], $from['name']);
            $data['from'] = $from;
            $this->_mail->setData($data);
        } else {
            $this->_mail->setFrom($from);
            $this->_mail->setReplyTo($from);
            $data['from'] = array(
                'email' => $from,
                'name' => ''
            );
            $this->_mail->setData($data);
        }
        $this->_mail->setTo($to);
        $this->_mail->setSubject($subject);

        if($this->_mail->send()) {
            return true;
        } else {
            return $this->_mail->getError();
        }
    }

    /**
     * Get the YiiMailer object.
     * @return YiiMailer.
     */
    public function getMailer()
    {
        if(!isset($this->_mail)) {
            $this->_mail = new YiiMailer();
            $this->_mail->IsSMTP();
            $this->_mail->Host = "mail.miama.org";
            $this->_mail->Port = 465;
            $this->_mail->SMTPAuth = true;
            $this->_mail->SMTPSecure = "ssl";
            $this->_mail->Mailer = "smtp";
            $this->_mail->Username = "rinkfinder@miama.org";
            $this->_mail->Password = "Rinkfinder2013#";
        }

        return $this->_mail;
    }
}
