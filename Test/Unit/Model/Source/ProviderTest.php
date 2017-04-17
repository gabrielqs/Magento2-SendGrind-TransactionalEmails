<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Unit\Model\Source;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\TransactionalEmails\Model\Source\Provider as Subject;

/**
 * Unit Testcase
 */
class ProviderTest extends \PHPUnit_Framework_TestCase
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
            ->setMethods(null)
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

    public function testToOptionArrayReturnsExpectedValue()
    {
        $return = $this->subject->toOptionArray();
        $this->assertInternalType('array', $return);
        $this->assertEquals(2, count($return));
        $this->assertEquals([
            [
                'value' => Subject::PROVIDER_SENDMAIL,
                'label' => 'sendmail',
                'class' => '\Magento\Framework\Mail\Transport'
            ],
            [
                'value' => Subject::PROVIDER_SENDGRID,
                'label' => 'SendGrid',
                'class' => '\Gabrielqs\TransactionalEmails\Model\Transports\SendGrid'
            ]
        ], $return);
    }

    public function dataProviderTestGetApiClassName()
    {
        return [
            [Subject::PROVIDER_SENDMAIL, '\Magento\Framework\Mail\Transport'],
            [Subject::PROVIDER_SENDGRID, '\Gabrielqs\TransactionalEmails\Model\Transports\SendGrid'],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetApiClassName
     */
    public function testGetApiClassName($providerCode, $providerClass)
    {
        $this->assertEquals($this->subject->getApiClassName($providerCode), $providerClass);
    }
}