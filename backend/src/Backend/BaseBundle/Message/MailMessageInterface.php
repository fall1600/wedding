<?php
namespace Backend\BaseBundle\Message;

interface MailMessageInterface
{
    /** @return String */
    public function getSubject();
    /** @return array */
    public function getEmails();
    /** @return String */
    public function getBody();
    /** @return String */
    public function getCharset();
    /** @return String */
    public function getContentType();
}
