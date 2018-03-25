<?php
/**
 * Message.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package dutchakdev\sendgrid
 */

namespace dutchakdev\sendgrid;


use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\mail\BaseMessage;
use Yii;
use yii\mail\MailerInterface;

/**
 * This component allow user to send an email
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package dutchakdev\sendgrid
 * @since XXX
 */
class Message extends BaseMessage
{
    /**
     * @var string|array from
     */
    protected $from;

    /**
     * @var array
     */
    protected $to = [];

    /**
     * @var string|array reply to
     */
    protected $replyTo;

    /**
     * @var array
     */
    protected $cc = [];

    /**
     * @var array
     */
    protected $bcc = [];

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $textBody;

    /**
     * @var string
     */
    protected $htmlBody;

    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var string temporary attachment directory
     */
    protected $attachmentsTmdDir;

    /**
     * @var array
     */
    protected $uniqueArguments = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $templateId;

    /**
     * @var array
     */
    protected $templateModel;

    /**
     * @var array substitution pairs used to mark expandable vars in template mode https://github.com/sendgrid/sendgrid-php#setsubstitutions
     */
    public $substitutionsPairs = ['{', '}'];


    protected $charset = 'UTF-8';
    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        return $this->charset;
    }
    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        $fromMail = null;
        reset($this->from);
        list($email, $name) = each($this->from);
        if (is_numeric($email) === true) {
            $fromMail = $name;
        } else {
            $fromMail = $email;
        }
        return $fromMail;
    }

    /**
     * @return string|null extract and return the name associated with from
     * @since XXX
     */
    public function getFromName()
    {
        reset($this->from);
        list($email, $name) = each($this->from);
        if (is_numeric($email) === false) {
            return $name;
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        if (is_string($from) === true) {
            $from = [$from];
        }
        $this->from = $from;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return self::normalizeEmails($this->to);
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        $replyTo = null;
        if (is_array($this->replyTo) === true) {
            reset($this->replyTo);
            list($email, $name) = each($this->replyTo);
            if (is_numeric($email) === true) {
                $replyTo = $name;
            } else {
                $replyTo = $email;
            }
        }
        return $replyTo;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        if (is_string($replyTo) === true) {
            $replyTo = [$replyTo];
        }
        $this->replyTo = $replyTo;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->cc = self::normalizeEmails($cc);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->bcc = self::normalizeEmails($bcc);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null text body of the message
     * @since XXX
     */
    public function getTextBody()
    {
        return $this->textBody;
    }

    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->textBody = $text;
        return $this;
    }

    /**
     * @return string|null html body of the message
     * @since XXX
     */
    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->htmlBody = $html;
        return $this;
    }

    /**
     * @return array list of unique arguments attached to the email
     * @since XXX
     */
    public function getUniqueArguments()
    {
        return $this->uniqueArguments;
    }

    /**
     * @param string $key key of the unique argument
     * @param string $value value of the unique argument which will be added to the mail
     * @return $this
     * @since XXX
     */
    public function addUniqueArgument($key, $value)
    {
        $this->uniqueArguments[$key] = $value;
        return $this;
    }

    /**
     * @param string $templateId template Id used. in this case, Subject / HtmlBody / TextBody are discarded
     * @return $this
     * @since XXX
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }

    /**
     * @return string|null current templateId
     * @since XXX
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param array $templateModel model associated with the template
     * @return $this
     * @since XXX
     */
    public function setTemplateModel($templateModel)
    {
        $this->templateModel = $templateModel;
        return $this;
    }

    /**
     * @return array current template model
     * @since XXX
     */
    public function getTemplateModel()
    {
        $templateModel = [];
        list($left, $right) = $this->substitutionsPairs;
        foreach ($this->templateModel as $key => $value) {
            if (is_array($value) === false) {
                $value = [$value];
            }
            $templateModel[$left.$key.$right] = $value;
        }
        return $templateModel;
    }

    /**
     * @param array $header add custom header to the mail
     * @since XXX
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * @return array|null headers which should be added to the mail
     * @since XXX
     */
    public function getHeaders()
    {
        return empty($this->headers) ? [] : $this->headers;
    }

    /**
     * @return array|null list of attachments
     * @since XXX
     */
    public function getAttachments()
    {
        return empty($this->attachments) ? [] : $this->attachments;
    }

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        $attachment = [
            'File' => $fileName
        ];
        if (!empty($options['fileName'])) {
            $attachment['Name'] = $options['fileName'];
        } else {
            $attachment['Name'] = pathinfo($fileName, PATHINFO_BASENAME);
        }
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * @return string temporary directory to store contents
     * @since XXX
     * @throws InvalidConfigException
     */
    protected function getTempDir()
    {
        if ($this->attachmentsTmdDir === null) {
            $uid = uniqid();
            $this->attachmentsTmdDir = Yii::getAlias('@app/runtime/'.$uid.'/');
            $status = true;
            if (file_exists($this->attachmentsTmdDir) === false) {
                $status = mkdir($this->attachmentsTmdDir, 0755, true);
            }
            if ($status === false) {
                throw new InvalidConfigException('Directory \''.$this->attachmentsTmdDir.'\' cannot be created');
            }
        }
        return $this->attachmentsTmdDir;
    }

    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        if (!isset($options['fileName']) || empty($options['fileName'])) {
            throw new InvalidParamException('Filename is missing');
        }
        $filePath = $this->getTempDir().'/'.$options['fileName'];
        if (file_put_contents($filePath, $content) === false) {
            throw new InvalidConfigException('Cannot write file \''.$filePath.'\'');
        }
        $this->attach($filePath, $options);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        $embed = [
            'File' => $fileName
        ];
        if (!empty($options['fileName'])) {
            $embed['Name'] = $options['fileName'];
        } else {
            $embed['Name'] = pathinfo($fileName, PATHINFO_BASENAME);
        }
        $embed['ContentID'] = 'cid:' . uniqid();
        $this->attachments[] = $embed;
        return $embed['ContentID'];
    }

    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        if (isset($options['fileName']) === false || empty($options['fileName'])) {
            throw new InvalidParamException('fileName is missing');
        }
        $filePath = $this->getTempDir().'/'.$options['fileName'];
        if (file_put_contents($filePath, $content) === false) {
            throw new InvalidConfigException('Cannot write file \''.$filePath.'\'');
        }
        $cid = $this->embed($filePath, $options);
        return $cid;
    }

    /**
     * @inheritdoc
     * @todo make real serialization to make message compliant with PostmarkAPI
     */
    public function toString()
    {
        return serialize($this);
    }


    /**
     * @param array|string $emailsData email can be defined as string. In this case no transformation is done
     *                                 or as an array ['email@test.com', 'email2@test.com' => 'Email 2']
     * @return string|null
     * @since XXX
     */
    public static function stringifyEmails($emailsData)
    {
        $emails = null;
        if (empty($emailsData) === false) {
            if (is_array($emailsData) === true) {
                foreach ($emailsData as $key => $email) {
                    if (is_int($key) === true) {
                        $emails[] = $email;
                    } else {
                        if (preg_match('/[.,:]/', $email) > 0) {
                            $email = '"'. $email .'"';
                        }
                        $emails[] = $email . ' ' . '<' . $key . '>';
                    }
                }
                $emails = implode(', ', $emails);
            } elseif (is_string($emailsData) === true) {
                $emails = $emailsData;
            }
        }
        return $emails;
    }

    public static function normalizeEmails($emailsData)
    {
        $emails = null;
        if (empty($emailsData) === false) {
            if (is_array($emailsData) === true) {
                foreach ($emailsData as $key => $email) {
                    if (is_int($key) === true) {
                        $emails[$email] = null;
                    } else {
                        $emails[$key] = $email;
                    }
                }
            } elseif (is_string($emailsData) === true) {
                $emails[$emailsData] = null;
            }
        }
        return $emails;
    }

    public function send(MailerInterface $mailer = null)
    {
        $result = parent::send($mailer);
        if ($this->attachmentsTmdDir !== null) {
            //TODO: clean up tmpdir after ourselves
        }
        return $result;
    }


}
