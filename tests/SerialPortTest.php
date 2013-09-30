<?php
/**
 * Test class
 *
 * Requires Sebastian Bergmann's PHPUnit
 *
 * PHP version 5
 *
 * @category
 * @package
 * @author    Lars Olesen <lars@legestue.net>
 * @copyright 2007 Authors
 * @license   GPL http://www.opensource.org/licenses/gpl-license.php
 * @version   @package-version@
 * @link      http://public.intraface.dk
 */
require_once dirname(__FILE__) . '/../src//Ilib/SerialPort.php';

/**
 * Test class
 *
 * @category
 * @package
 * @author    Lars Olesen <lars@legestue.net>
 * @copyright 2007 Authors
 * @license   GPL http://www.opensource.org/licenses/gpl-license.php
 * @version   @package-version@
 * @link      http://public.intraface.dk
 */
class SerialPortTest extends PHPUnit_Framework_TestCase
{
    private $port;
    function setUp()
    {
        $this->port = new Ilib_SerialPort();
    }

    function tearDown()
    {
        unset($this->port);
    } 

    function testConstructionWorksWithNoArguments()
    {
        $this->assertTrue(is_object($this->port));
    }

    function testDeviceSetThrowsExceptionWhenInvalidPortIsSet()
    {
        try {
            $this->port->deviceSet('INVALID');
        } catch (Exception $e) {
            return;
        }
        $this->fail('An exception should have been raised');
    }
    
    function testDeviceOpen()
    {
        $this->port->deviceOpen();
    }   

    function testDeviceClose()
    {
        $this->port->deviceClose();
    }   

    function testConfBaudRate()
    {
        $this->port->confBaudRate();
    }

    function testConfParity()
    {
        $this->port->confParity();
    }

    function testConfCharacterLength()
    {
        $this->port->confCharacterLength();
    }

    function testConfStopBits()
    {
        $this->port->confStopBits();
    }

    function testConfFlowControl()
    {
        $this->port->confFlowControl();
    }

    function testSetSetserialFlag()
    {
        $this->port->setSetserialFlag();
    }

    function testReadPort()
    {
        $this->port->readPort();
    }

    function testSendMessage()
    {
        $this->port->sendMessage();
    }

    function testflush()
    {
        $this->port->flush();
    }
}

