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
        $rate = NULL;
        $this->port->confBaudRate($rate);
    }

    function testConfParity()
    {
        $parity = NULL;
        $this->port->confParity($parity);
    }

    function testConfCharacterLength()
    {
        $length = NULL;
        $this->port->confCharacterLength($length);
    }

    function testConfStopBits()
    {
        $bits = NULL;
        $this->port->confStopBits($bits);
    }

    function testConfFlowControl()
    {
        $flow = NULL;
        $this->port->confFlowControl($flow);
    }

    function testSetSetserialFlag()
    {
        $flag = NULL;
        $this->port->setSetserialFlag($flag);
    }

    function testReadPort()
    {
        $this->port->readPort();
    }

    function testSendMessage()
    {
        $message = NULL;
        $this->port->sendMessage($message);
    }

    function testflush()
    {
        $this->port->flush();
    }
}

