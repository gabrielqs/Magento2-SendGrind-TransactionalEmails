<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Block\Adminhtml\Order\View\Tab;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\TransactionalEmails\Block\Adminhtml\Order\View\Tab\TransactionalEmails as Subject;

/**
 * Unit Testcase
 */
class TransactionalEmailsTest extends \PHPUnit_Framework_TestCase
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
     * Subject
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
            ->setMethods(['getAllowedResources'])
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

    public function dataProviderTestCanShowTab()
    {
        return [
            [['Gabrielqs_TransactionalEmails::orderEmailsList', 'Magento_Backend::all'], true],
            [['Gabrielqs_TransactionalEmails::orderEmailsList'], true],
            [['Magento_Backend::all'], true],
            [['Won\'t Allow'], false],
            [[], false],
        ];
    }

    /**
     * @dataProvider dataProviderTestCanShowTab
     */
    public function testCanShowTab($resources, $expectedReturn)
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getAllowedResources')
            ->willReturn($resources);
        $this->assertEquals($this->subject->canShowTab(), $expectedReturn);
    }

}