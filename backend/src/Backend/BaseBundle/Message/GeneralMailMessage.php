<?php
namespace Backend\BaseBundle\Message;

class GeneralMailMessage implements MailMessageInterface
{
    protected $emails;
    protected $subject;
    protected $body;
    protected $charset;
    protected $contentType;

    public function __construct($emails, $subject, $body, $charset = "utf-8", $contentType = "text/html")
    {
        $this->emails = $emails;
        $this->subject = $subject;
        $this->body = $body;
        $this->charset = $charset;
        $this->contentType = $contentType;
    }

    /**
     * @return array
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
