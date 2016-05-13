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
    function getDevice()
    {
        return new Ilib_SerialPort();
    }

    protected function getSetDevice()
    {
        $port = $this->getDevice();
        try {
            return $port->deviceSet('/dev/ttyS0');
        } catch (Exception $e) {
            $this->markTestSkipped('Cannot set any device');
        }
    }

    function testConstructionWorksWithNoArguments()
    {
        $this->assertTrue(is_object($this->getDevice()));
    }

    function testDeviceSetThrowsExceptionWhenInvalidPortIsSet()
    {
        try {
            $this->getDevice()->deviceSet('INVALID');
        } catch (Exception $e) {
            return;
        }
        $this->fail('An exception should have been raised');
    }

    function testDeviceOpen()
    {
        $this->getSetDevice()->deviceOpen();
    }

    function testDeviceClose()
    {
        $this->getSetDevice()->deviceClose();
    }

    function testConfBaudRate()
    {
        $rate = null;
        $this->getSetDevice()->confBaudRate($rate);
    }

    function testConfParity()
    {
        $parity = null;
        $this->getSetDevice()->confParity($parity);
    }

    function testConfCharacterLength()
    {
        $length = null;
        $this->getSetDevice()->confCharacterLength($length);
    }

    function testConfStopBits()
    {
        $bits = null;
        $this->getSetDevice()->confStopBits($bits);
    }

    function testConfFlowControl()
    {
        $flow = null;
        $this->getSetDevice()->confFlowControl($flow);
    }

    function testSetSetserialFlag()
    {
        $flag = null;
        $this->getSetDevice()->setSetserialFlag($flag);
    }

    function testReadPort()
    {
        $this->getSetDevice()->readPort();
    }

    function testSendMessage()
    {
        $message = null;
        $this->getSetDevice()->sendMessage($message);
    }

    function testflush()
    {
        $this->getSetDevice()->flush();
    }
}
