<?php

namespace Gabrielqs\TransactionalEmails\Controller\Adminhtml\Sent;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Backend\Model\View\Result\PageFactory;
use \Magento\Backend\Model\View\Result\Page;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\Sent\Index as Subject;

/**
 * Unit Testcase
 */
class IndexTest extends \PHPUnit_Framework_TestCase
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
     * @var Page
     */
    protected $page = null;

    /**
     * @var PageFactory
     */
    protected $pageFactory = null;

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
            ->setMethods(['initPage', '_prependTitle'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->pageFactory = $this->getMockBuilder(PageFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['resultPageFactory'] = $this->pageFactory;

        $this->page = $this->getMockBuilder(Page::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this
            ->pageFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->page);

        return $arguments;
    }

    public function testExecute()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('initPage')
            ->with($this->page)
            ->willReturn($this->subject);
        $this
            ->subject
            ->expects($this->once())
            ->method('_prependTitle')
            ->with($this->page, __('Sent E-mails'))
            ->willReturn($this->subject);
        $this->assertEquals($this->subject->execute(), $this->page);
    }

}