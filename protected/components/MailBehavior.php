<?php
/**
 * MailBehavior class file.
 * @author Vincent Palodichuk <vj.palodichuk@gmail.com>
 * @copyright Copyright &copy; Vincent Palodichuk 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
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
	 * $mail->sendMail('', 'john@example.com', 'sub', 'msg', 'view');
	 * $mail->sendMail(array('name'=>'John Doe',
	 *                       'email'=>'john@example.com'),
	 *                 array('john@example.com',
	 *                       'jane@example.com'),
	 *                 'sub', 'msg', 'view');
	 * $mail->sendMail('john@example.com',
	 *                 array('john@example.com'=>'John Doe',
	 *                       'jane@example.com'),
	 *                 'sub', 'msg', 'view');
     * @param mixed $from address of the recipient.
     * @param mixed $to address of the recipient.
     * @param string $subject subject of the message.
     * @param string $message content of the message.
     * @param string $view the view to use, defaults to contact.
     * @return mixed true if successful or the error message received.
     */
	public function sendMail($from, $to, $subject, $message, $view = 'contact')
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

	    $message = wordwrap($message, 70);
	    $message = str_replace("\n.", "\n..", $message);

        //set properties
        //use 'contact' view from views/mail by defaul
        $this->_mail->setView($view);

        if($from == '') {
            $this->_mail->setFrom(Yii::app()->params['adminEmail'], 'Rinkfinder.com');
            $this->_mail->setReplyTo(Yii::app()->params['adminEmail'], 'Rinkfinder.com');
            $this->_mail->setData(array('message' => $message, 'name' => 'Rinkfinder.com', 'description' => $subject));
        } else {
            if(is_array($from)) {
                $this->_mail->setFrom($from['email'], $from['name']);
                $this->_mail->setReplyTo($from['email'], $from['name']);
                $this->_mail->setData(array('message' => $message, 'name' => $from['name'] . ' (' . $from['email'] . ')', 'description' => $subject));
            } else {
                $this->_mail->setFrom($from);
                $this->_mail->setReplyTo($from);
                $this->_mail->setData(array('message' => $message, 'name' => $from, 'description' => $subject));
            }
        }
        $this->_mail->setSubject($subject);
        $this->_mail->setTo($to);

	    if($this->_mail->send()) {
            return true;
        } else {
            return $this->_mail->getError();
        }
	}

    /**
     * @desc Get the YiiMailer object.
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
