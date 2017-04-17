<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Model;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\TransactionalEmails\Model\Message as Subject;

/**
 * Unit Testcase
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

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
            ->setMethods(['getHeaders'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);
        return $arguments;
    }

    public function testGetBccAsStringReturnsExpectedValueFromHeaders()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(
                [
                    Subject::HEADER_BCC => ['Should', 'Return', 'This'],
                    Subject::HEADER_FROM => ['Not This'],
                    Subject::HEADER_TO => ['Not This'],
                ]
            );
        $this->assertEquals('Should, Return, This', $this->subject->getBccAsAString());
    }

    public function testGetBccAsStringReturnsEmptyStringWhenNoBccSet()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(
                [
                    Subject::HEADER_BCC => null,
                    Subject::HEADER_FROM => ['Not This'],
                    Subject::HEADER_TO => ['Not This'],
                ]
            );
        $this->assertEquals('', $this->subject->getBccAsAString());
    }

    public function testGetAndSetOrderId()
    {
        $orderId = '84847';
        $this
            ->subject
            ->setOrderId($orderId);
        $this->assertEquals((int) $orderId, $this->subject->getOrderId());
    }

    public function testGetAndSetResponse()
    {
        $response = '{success: false}';
        $this
            ->subject
            ->setResponse($response);
        $this->assertEquals($response, $this->subject->getResponse());
    }

    public function testGetAndSetRequest()
    {
        $request = '{sendTo: "test@example.com"}';
        $this
            ->subject
            ->setRequest($request);
        $this->assertEquals($request, $this->subject->getRequest());
    }

    public function testGetToReturnsRightInfoFromHeaders()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(
                [
                    Subject::HEADER_BCC => ['Should', 'Not', 'Return', 'This'],
                    Subject::HEADER_FROM => ['Not This'],
                    Subject::HEADER_TO => ['Should <should@returnthis.com>'],
                ]
            );
        $this->assertEquals('Should <should@returnthis.com>', $this->subject->getTo());
    }

    public function testGetToEmailReturnsRightInfoFromHeaders()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(
                [
                    Subject::HEADER_BCC => ['Should', 'Not', 'Return', 'This'],
                    Subject::HEADER_FROM => ['Not This'],
                    Subject::HEADER_TO => ['Should <should@returnthis.com>'],
                ]
            );
        $this->assertEquals('should@returnthis.com', $this->subject->getToEmail());
    }

    public function testGetToEmailReturnsRightInfoFromHeadersWhenNoNamePresent()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn(
                [
                    Subject::HEADER_BCC => ['Should', 'Not', 'Return', 'This'],
                    Subject::HEADER_FROM => ['Not This'],
                    Subject::HEADER_TO => ['should@returnthis.com'],
                ]
            );
        $this->assertEquals('should@returnthis.com', $this->subject->getToEmail());
    }

}