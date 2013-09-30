<?php
/**
 * Serial port control class
 *
 * Port to PHP5 of phpSerial found on http://www.phpclasses.org/browse/file/17926.html
 * authored by Rémy Sanchez <thenux@gmail.com>.
 *
 * PHP version 5
 *
 * @package   Ilib_SerialPort
 * @author    Rémy Sanchez <thenux@gmail.com>
 * @author    Lars Olesen <lars@legestue.net>
 * @copyright GPL 2 licence
 * @link
 */

/**
 * Serial port control class
 *
 * @package Ilib_SerialPort
 * @author Rémy Sanchez <thenux@gmail.com>
 * @author Lars Olesen <lars@legestue.net>
 * @copyright under GPL 2 licence
 */
class Ilib_SerialPort
{
    /**
     * @var const SERIAL_DEVICE_NOTSET
     */
    const SERIAL_DEVICE_NOTSET = 0;

    /**
     * @var const SERIAL_DEVICE_SET
     */
    const SERIAL_DEVICE_SET = 1;

    /**
     * @var const SERIAL_DEVICE_OPENED
     */
    const SERIAL_DEVICE_OPENED = 2;

    protected $_device = null;
    protected $_windevice = null;
    protected $_dHandle = null;
    protected $_dState = self::SERIAL_DEVICE_NOTSET;
    protected $_buffer = "";
    protected $_os = "";

    /**
     * This var says if buffer should be flushed by sendMessage (true) or manualy (false)
     *
     * @var bool
     */
    protected $autoflush = true;

    /**
     * Constructor. Perform some checks about the OS and setserial
     *
     * @return phpSerial
     */
    public function __construct()
    {
        setlocale(LC_ALL, "en_US");

        $sysname = php_uname();

        if (substr($sysname, 0, 5) === "Linux") {
            $this->_os = "linux";
            if ($this->_exec("stty --version") === 0) {
                register_shutdown_function(array($this, "deviceClose"));
            } else {
                throw new Exception("No stty availible, unable to run.");
            }
        } elseif (substr($sysname, 0, 7) === "Windows") {
            $this->_os = "windows";
            register_shutdown_function(array($this, "deviceClose"));
        } else {
            throw new Exception("Host OS is neither linux nor windows, unable to run.");
        }
    }

    /**
     * Device set function : used to set the device name/address.
     * -> linux : use the device address, like /dev/ttyS0
     * -> windows : use the COMxx device name, like COM1 (can also be used
     *     with linux)
     *
     * @param string $device the name of the device to be used
     *
     * @return bool
     */
    public function deviceSet($device)
    {
        if ($this->_dState !== self::SERIAL_DEVICE_OPENED) {
            if ($this->_os === "linux") {
                if (preg_match("@^COM(\d+):?$@i", $device, $matches)) {
                    $device = "/dev/ttyS" . ($matches[1] - 1);
                }

                if ($this->_exec("stty -F " . $device) === 0) {
                    $this->_device = $device;
                    $this->_dState = self::SERIAL_DEVICE_SET;
                    return true;
                }
            } elseif ($this->_os === "windows") {
                if (preg_match("@^COM(\d+):?$@i", $device, $matches) and $this->_exec(exec("mode " . $device)) === 0) {
                    $this->_windevice = "COM" . $matches[1];
                    $this->_device = "\\.\com" . $matches[1];
                    $this->_dState = self::SERIAL_DEVICE_SET;
                    return true;
                }
            }

            throw new Exception("Specified serial port is not valid");
        } else {
            throw new Exception("You must close your device before to set an other one");
        }
    }

    /**
     * Opens the device for reading and/or writing.
     *
     * @param string $mode Opening mode : same parameter as fopen()
     *
     * @return bool
     */
    public function deviceOpen($mode = "r+b")
    {
        if ($this->_dState === self::SERIAL_DEVICE_OPENED) {
            throw new Exeption("The device is already opened");
        }

        if ($this->_dState === self::SERIAL_DEVICE_NOTSET) {
            throw new Exception("The device must be set before to be open");
        }

        if (!preg_match("@^[raw]\+?b?$@", $mode)) {
            throw new Exception("Invalid opening mode : ".$mode.". Use fopen() modes.");
        }

        $this->_dHandle = @fopen($this->_device, $mode);

        if ($this->_dHandle !== false) {
            stream_set_blocking($this->_dHandle, 0);
            $this->_dState = self::SERIAL_DEVICE_OPENED;
            return true;
        }

        $this->_dHandle = null;
        throw new Exception("Unable to open the device");
    }

    /**
     * Closes the device
     *
     * @return bool
     */
    public function deviceClose()
    {
        if ($this->_dState !== self::SERIAL_DEVICE_OPENED) {
            return true;
        }

        if (fclose($this->_dHandle)) {
            $this->_dHandle = null;
            $this->_dState = self::SERIAL_DEVICE_SET;
            return true;
        }

        throw new Exception("Unable to close the device");
    }

    /**
     * Configure the Baud Rate
     * Possible rates : 110, 150, 300, 600, 1200, 2400, 4800, 9600, 38400,
     * 57600 and 115200.
     *
     * @param int $rate the rate to set the port in
     *
     * @return bool
     */
    public function confBaudRate($rate)
    {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set the baud rate : the device is either not set or opened");
        }

        $validBauds = array (
            110    => 11,
            150    => 15,
            300    => 30,
            600    => 60,
            1200   => 12,
            2400   => 24,
            4800   => 48,
            9600   => 96,
            19200  => 19,
            38400  => 38400,
            57600  => 57600,
            115200 => 115200
        );

        if (isset($validBauds[$rate])) {
            if ($this->_os === "linux") {
                $ret = $this->_exec("stty -F " . $this->_device . " " . (int) $rate, $out);
            } elseif ($this->_os === "windows") {
                $ret = $this->_exec("mode " . $this->_windevice . " BAUD=" . $validBauds[$rate], $out);
            } else {
                return false;
            }

            if ($ret !== 0) {
                throw new Exception("Unable to set baud rate: " . $out[1]);
            }
        }
    }

    /**
     * Configure parity.
     * Modes : odd, even, none
     *
     * @param string $parity one of the modes
     * @return bool
     */
    public function confParity($parity)
    {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set parity : the device is either not set or opened");
        }

        $args = array(
            "none" => "-parenb",
            "odd"  => "parenb parodd",
            "even" => "parenb -parodd",
        );

        if (!isset($args[$parity])) {
            throw new Exception("Parity mode not supported");
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . $args[$parity], $out);
        } else {
            $ret = $this->_exec("mode " . $this->_windevice . " PARITY=" . $parity{0}, $out);
        }

        if ($ret === 0) {
            return true;
        }

        throw new Exception("Unable to set parity : " . $out[1]);
    }

    /**
     * Sets the length of a character.
     *
     * @param int $int length of a character (5 <= length <= 8)
     *
     * @return bool
     */
    public function confCharacterLength ($int)
    {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set length of a character : the device is either not set or opened");
        }

        $int = (int) $int;
        if ($int < 5) {
            $int = 5;
        } elseif ($int > 8) {
            $int = 8;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " cs" . $int, $out);
        } else {
            $ret = $this->_exec("mode " . $this->_windevice . " DATA=" . $int, $out);
        }

        if ($ret === 0) {
            return true;
        }

        throw new Exception("Unable to set character length : " .$out[1]);
    }

    /**
     * Sets the length of stop bits.
     *
     * @param float $length the length of a stop bit. It must be either 1,
     * 1.5 or 2. 1.5 is not supported under linux and on some computers.
     *
     * @return bool
     */
    public function confStopBits($length)
    {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set the length of a stop bit : the device is either not set or opened");
        }

        if ($length != 1 and $length != 2 and $length != 1.5 and !($length == 1.5 and $this->_os === "linux")) {
            throw new Exception("Specified stop bit length is invalid");
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . (($length == 1) ? "-" : "") . "cstopb", $out);
        } else {
            $ret = $this->_exec("mode " . $this->_windevice . " STOP=" . $length, $out);
        }

        if ($ret === 0) {
            return true;
        }

        throw new Exception("Unable to set stop bit length : " . $out[1]);
    }

    /**
     * Configures the flow control
     *
     * @param string $mode Set the flow control mode. Availible modes :
     *  -> "none" : no flow control
     *  -> "rts/cts" : use RTS/CTS handshaking
     *  -> "xon/xoff" : use XON/XOFF protocol
     *
     * @return bool
     */
    public function confFlowControl($mode)
    {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set flow control mode : the device is either not set or opened");
        }

        $linuxModes = array(
            "none"     => "clocal -crtscts -ixon -ixoff",
            "rts/cts"  => "-clocal crtscts -ixon -ixoff",
            "xon/xoff" => "-clocal -crtscts ixon ixoff"
        );
        $windowsModes = array(
            "none"     => "xon=off octs=off rts=on",
            "rts/cts"  => "xon=off octs=on rts=hs",
            "xon/xoff" => "xon=on octs=off rts=on",
        );

        if ($mode !== "none" and $mode !== "rts/cts" and $mode !== "xon/xoff") {
            throw new Exception("Invalid flow control mode specified");
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . $linuxModes[$mode], $out);
        } else {
            $ret = $this->_exec("mode " . $this->_windevice . " " . $windowsModes[$mode], $out);
        }

        if ($ret === 0) {
            return true;
        } else {
            throw new Exception("Unable to set flow control : " . $out[1]);
        }
    }

    /**
     * Sets a setserial parameter (cf man setserial)
     * NO MORE USEFUL !
     *  -> No longer supported
     *  -> Only use it if you need it
     *
     * @param string $param parameter name
     * @param string $arg parameter value
     *
     * @return bool
     */
    public function setSetserialFlag ($param, $arg = "")
    {
        if (!$this->_ckOpened()) {
            return false;
        }

        $return = exec ("setserial " . $this->_device . " " . $param . " " . $arg . " 2>&1");

        if ($return{0} === "I") {
            throw new Exception("setserial: Invalid flag");
        } elseif ($return{0} === "/") {
            throw new Exception("setserial: Error with device file");
        } else {
            return true;
        }
    }

    /**
     * Sends a string to the device
     *
     * @param string $str string to be sent to the device
     * @param float $waitForReply time to wait for the reply (in seconds)
     */
    public function sendMessage($str, $waitForReply = 0.1)
    {
        $this->_buffer .= $str;

        if ($this->autoflush === true) $this->flush();

        usleep((int) ($waitForReply * 1000000));
    }

    /**
     * Reads the port until no new datas are availible, then return the content.
     *
     * @param int $count number of characters to be read (will stop before
     *  if less characters are in the buffer)
     * @return string
     */
    public function readPort($count = 0)
    {
        if ($this->_dState !== self::SERIAL_DEVICE_OPENED) {
            throw new Exception("Device must be opened to read it");
        }

        if ($this->_os === "linux") {
            $content = ""; $i = 0;

            if ($count !== 0) {
                do {
                    if ($i > $count) {
                        $content .= fread($this->_dHandle, ($count - $i));
                    } else {
                        $content .= fread($this->_dHandle, 128);
                    }
                } while (($i += 128) === strlen($content));
            } else {
                do {
                    $content .= fread($this->_dHandle, 128);
                } while (($i += 128) === strlen($content));
            }

            return $content;
        } elseif ($this->_os === "windows") {
            // So far only implemented without possible to set count.
            $content = ''; $i = 0;
            do {
                $content .= fread($this->_dHandle, 128);
            } while (($i += 128) === strlen($content));

            return $content;
        }

        return false;
    }

    /**
     * Flushes the output buffer
     *
     * @return bool
     */
    public function flush()
    {
        if (!$this->_ckOpened()) {
            return false;
        }

        if (fwrite($this->_dHandle, $this->_buffer) !== false) {
            $this->_buffer = "";
            return true;
        } else {
            $this->_buffer = "";
            throw new Exception("Error while sending message");
        }
    }

    /**
     * Checks if serial port is opened
     */
    private function _ckOpened()
    {
        if ($this->_dState !== self::SERIAL_DEVICE_OPENED) {
            throw new Exception("Device must be opened");
        }

        return true;
    }

    /**
     * Executes commands
     *
     * @return boolean
     */
    protected function _exec($cmd, &$out = null)
    {
        $desc = array(
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $proc = proc_open($cmd, $desc, $pipes);

        $ret = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $retVal = proc_close($proc);

        if (func_num_args() == 2) {
            $out = array($ret, $err);
        }
        return $retVal;
    }
}
