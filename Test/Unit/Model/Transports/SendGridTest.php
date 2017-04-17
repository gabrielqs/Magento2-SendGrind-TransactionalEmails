<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Model\Transports;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Framework\Exception\LocalizedException;
use \Gabrielqs\TransactionalEmails\Model\Transports\SendGrid as Subject;
use \Gabrielqs\TransactionalEmails\Model\Message as GabrielqsMessage;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;
use \SendGrid as SendGridApi;
use \SendGrid\Response as SendGridResponse;
use \SendGrid\Mail as SendGridMailHelper;
use \SendGrid\MailFactory as SendGridMailHelperFactory;
use \SendGrid\Email as SendGridEmail;
use \SendGrid\EmailFactory as SendGridEmailFactory;
use \SendGrid\Content as SendGridContent;
use \SendGrid\Personalization as SendGridPersonalization;
use \SendGrid\ContentFactory as SendGridContentFactory;
use \Zend_Mime_Part;

/**
 * Unit Testcase
 */
class SendGridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * SendGrid Email BCC A
     * @var SendGridEmail
     */
    protected $bccA;

    /**
     * SendGrid Email BCC B
     * @var SendGridEmail
     */
    protected $bccB;

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var SendGridContent
     */
    protected $content = null;

    /**
     * SendGrid Email From
     * @var SendGridEmail
     */
    protected $from;

    /**
     * Data Helper
     * @var Helper
     */
    protected $helper;

    /**
     * @var GabrielqsMessage
     */
    protected $message = null;

    /**
     * @var Zend_Mime_Part
     */
    protected $messageBody = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * SendGrid Content Factory
     * @var SendGridContentFactory
     */
    protected $sendGridContentFactory;


    /**
     * SendGrid Email Factory
     * @var SendGridEmailFactory
     */
    protected $sendGridEmailFactory;

    /**
     * SendGrid Mail Helper
     * @var SendGridMailHelper
     */
    protected $sendGridMailHelper;

    /**
     * SendGrid Mail Helper Factory
     * @var SendGridMailHelperFactory
     */
    protected $sendGridMailHelperFactory;

    /**
     * SendGrid Personalization
     * @var SendGridPersonalization
     */
    protected $sendGridPersonalization;

    /**
     * SendGrid Response
     * @var SendGridResponse
     */
    protected $sendGridResponse;

    /**
     * SendGrid Email To
     * @var SendGridEmail
     */
    protected $to;

    /**
     * @var Subject
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = Subject::class;
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(['_getSendGridPersonalization', '_makeSendGridRequest'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);

        $this->messageBody = $this->getMockBuilder(Zend_Mime_Part::class)
            ->setMethods(['getHeaders', 'getRawContent'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->message = $this->getMockBuilder(GabrielqsMessage::class)
            ->setMethods(['getHeader', 'getBody', 'getSubject', 'setRequest', 'setResponse'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->sendGridMailHelper = $this->getMockBuilder(SendGridMailHelper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->sendGridPersonalization = $this->getMockBuilder(SendGridPersonalization::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->to = $this->getMockBuilder(SendGridEmail::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->from = $this->getMockBuilder(SendGridEmail::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->bccA = $this->getMockBuilder(SendGridEmail::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->bccB = $this->getMockBuilder(SendGridEmail::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->sendGridResponse = $this->getMockBuilder(SendGridResponse::class)
            ->setMethods(['statusCode', 'body'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->helper = $this->getMockBuilder(Helper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['helper'] = $this->helper;

        $this->sendGridEmailFactory = $this->getMockBuilder(SendGridEmailFactory::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['sendGridEmailFactory'] = $this->sendGridEmailFactory;

        $this->sendGridContentFactory = $this->getMockBuilder(SendGridContentFactory::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['sendGridContentFactory'] = $this->sendGridContentFactory;

        $this->sendGridMailHelperFactory = $this->getMockBuilder(SendGridMailHelperFactory::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['sendGridMailHelperFactory'] = $this->sendGridMailHelperFactory;

        return $arguments;
    }

    public function testSendSuccessFromWithNameHtmlMessage()
    {
        $fromName = 'John Doe';
        $fromEmail = 'john@doe.com';
        $toEmail = 'foo@baz.com';
        $subject = 'Foo Bar';
        $content = '<p>Hello</p>';
        $contentType = Subject::CONTENT_TYPE_HTML;
        $bccA = 'bcca@baz,ccom';
        $bccB = 'bccb@baz.com';

        $this
            ->message
            ->expects($this->exactly(3))
            ->method('getHeader')
            ->withConsecutive(
                [GabrielqsMessage::HEADER_FROM],
                [GabrielqsMessage::HEADER_TO],
                [GabrielqsMessage::HEADER_BCC]
            )->willReturnOnConsecutiveCalls(
                [0 => $fromName . ' <' . $fromEmail . '>'],
                [0 => $toEmail],
                [0 => $bccA, 1 => $bccB]
            );

        $this
            ->sendGridEmailFactory
            ->expects($this->exactly(4))
            ->method('create')
            ->withConsecutive(
                [['name' => $fromName, 'email' => $fromEmail]],
                [['name' => '', 'email' => $toEmail]],
                [['name' => '', 'email' => $bccA]],
                [['name' => '', 'email' => $bccB]]
            )->willReturnOnConsecutiveCalls(
                $this->from,
                $this->to,
                $this->bccA,
                $this->bccB
            );

        $this
            ->message
            ->expects($this->once())
            ->method('getSubject')
            ->willReturn($subject);

        $this
            ->message
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->messageBody);

        $this
            ->messageBody
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn($contentType);

        $this
            ->messageBody
            ->expects($this->once())
            ->method('getRawContent')
            ->willReturn($content);

        $this
            ->sendGridContentFactory
            ->expects($this->once())
            ->method('create')
            ->with(['type' => Subject::CONTENT_TYPE_HTML, 'value' => $content]);

        $this
            ->subject
            ->expects($this->exactly(2))
            ->method('_getSendGridPersonalization')
            ->withConsecutive(
                [$this->sendGridMailHelper, null],
                [$this->sendGridMailHelper, null]
            )->willReturnOnConsecutiveCalls(
                $this->sendGridPersonalization,
                $this->sendGridPersonalization
            );

        $this
            ->sendGridPersonalization
            ->expects($this->exactly(2))
            ->method('addBcc')
            ->withConsecutive(
                [$this->bccA],
                [$this->bccB]
            );

        $this
            ->sendGridMailHelperFactory
            ->expects($this->once())
            ->method('create')
            ->with(['from' => $this->from, 'subject' => $subject, 'to' => $this->to, 'content' => $this->content])
            ->willReturn($this->sendGridMailHelper);

        $this
            ->subject
            ->expects($this->once())
            ->method('_makeSendGridRequest')
            ->with($this->sendGridMailHelper)
            ->willReturn($this->sendGridResponse);

        $this
            ->message
            ->expects($this->once())
            ->method('setRequest')
            ->with(json_encode($this->sendGridMailHelper));

        $this
            ->message
            ->expects($this->once())
            ->method('setResponse')
            ->with(json_encode($this->sendGridResponse));

        $this->sendGridResponse
            ->expects($this->once())
            ->method('statusCode')
            ->willReturn(Subject::SUCCESS_STATUS_CODE);

        $this->subject->send($this->message);
    }

    public function testSendFailureFromWithNoNamePlainTextMessage()
    {
        $fromEmail = 'john@doe.com';
        $toEmail = 'foo@baz.com';
        $subject = 'Foo Bar';
        $content = '<p>Hello</p>';
        $contentType = Subject::CONTENT_TYPE_PLAIN;
        $bccA = 'bcca@baz,ccom';
        $bccB = 'bccb@baz.com';

        $this
            ->message
            ->expects($this->exactly(3))
            ->method('getHeader')
            ->withConsecutive(
                [GabrielqsMessage::HEADER_FROM],
                [GabrielqsMessage::HEADER_TO],
                [GabrielqsMessage::HEADER_BCC]
            )->willReturnOnConsecutiveCalls(
                [0 => $fromEmail],
                [0 => $toEmail],
                [0 => $bccA, 1 => $bccB]
            );

        $this
            ->sendGridEmailFactory
            ->expects($this->exactly(4))
            ->method('create')
            ->withConsecutive(
                [['name' => '', 'email' => $fromEmail]],
                [['name' => '', 'email' => $toEmail]],
                [['name' => '', 'email' => $bccA]],
                [['name' => '', 'email' => $bccB]]
            )->willReturnOnConsecutiveCalls(
                $this->from,
                $this->to,
                $this->bccA,
                $this->bccB
            );

        $this
            ->message
            ->expects($this->once())
            ->method('getSubject')
            ->willReturn($subject);

        $this
            ->message
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->messageBody);

        $this
            ->messageBody
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn($contentType);

        $this
            ->messageBody
            ->expects($this->once())
            ->method('getRawContent')
            ->willReturn($content);

        $this
            ->sendGridContentFactory
            ->expects($this->once())
            ->method('create')
            ->with(['type' => Subject::CONTENT_TYPE_PLAIN, 'value' => $content]);

        $this
            ->subject
            ->expects($this->exactly(2))
            ->method('_getSendGridPersonalization')
            ->withConsecutive(
                [$this->sendGridMailHelper, null],
                [$this->sendGridMailHelper, null]
            )->willReturnOnConsecutiveCalls(
                $this->sendGridPersonalization,
                $this->sendGridPersonalization
            );

        $this
            ->sendGridPersonalization
            ->expects($this->exactly(2))
            ->method('addBcc')
            ->withConsecutive(
                [$this->bccA],
                [$this->bccB]
            );

        $this
            ->sendGridMailHelperFactory
            ->expects($this->once())
            ->method('create')
            ->with(['from' => $this->from, 'subject' => $subject, 'to' => $this->to, 'content' => $this->content])
            ->willReturn($this->sendGridMailHelper);

        $this
            ->subject
            ->expects($this->once())
            ->method('_makeSendGridRequest')
            ->with($this->sendGridMailHelper)
            ->willReturn($this->sendGridResponse);

        $this
            ->message
            ->expects($this->once())
            ->method('setRequest')
            ->with(json_encode($this->sendGridMailHelper));

        $this
            ->message
            ->expects($this->once())
            ->method('setResponse')
            ->with(json_encode($this->sendGridResponse));

        $this
            ->sendGridResponse
            ->expects($this->once())
            ->method('statusCode')
            ->willReturn(0);

        $this
            ->sendGridResponse
            ->expects($this->once())
            ->method('body')
            ->willReturn('{"errors": [{"message": "Lorem", "field": "from"}, {"message": "Ipsum", "field": "To"}]}');

        $this->setExpectedException(LocalizedException::class);

        $this->subject->send($this->message);
    }

}