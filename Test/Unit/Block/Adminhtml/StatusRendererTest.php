<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Block\Adminhtml;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\TransactionalEmails\Model\Email;
use \Gabrielqs\TransactionalEmails\Block\Adminhtml\StatusRenderer as Subject;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;

/**
 * Unit Testcase
 */
class StatusRendererTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var Helper
     */
    protected $helper = null;

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
            ->setMethods(null)
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['getStatusLabel'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['helper'] = $this->helper;

        return $arguments;
    }

    public function testRender()
    {
        $status = 'Failure';

        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this
            ->helper
            ->expects($this->once())
            ->method('getStatusLabel')
            ->with($email)
            ->willReturn($status);

        $this->assertEquals($this->subject->render($email), $status);
    }

}