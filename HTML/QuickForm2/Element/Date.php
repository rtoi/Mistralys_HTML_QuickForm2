<?php
/**
 * Date element
 *
 * PHP version 5
 *
 * LICENSE
 *
 * This source file is subject to BSD 3-Clause License that is bundled
 * with this package in the file LICENSE and available at the URL
 * https://raw.githubusercontent.com/pear/HTML_QuickForm2/trunk/docs/LICENSE
 *
 * @category  HTML
 * @package   HTML_QuickForm2
 * @author    Alexey Borzov <avb@php.net>
 * @author    Bertrand Mansion <golgote@mamasam.com>
 * @copyright 2006-2020 Alexey Borzov <avb@php.net>, Bertrand Mansion <golgote@mamasam.com>
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link      https://pear.php.net/package/HTML_QuickForm2
 */

declare(strict_types=1);

/**
 * Class for a group of elements used to input dates (and times).
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Alexey Borzov <avb@php.net>
 * @author   Bertrand Mansion <golgote@mamasam.com>
 * @license  https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version  Release: @package_version@
 * @link     https://pear.php.net/package/HTML_QuickForm2
 */
class HTML_QuickForm2_Element_Date extends HTML_QuickForm2_Container_Group
{
    public const ERROR_INVALID_MESSAGE_PROVIDER = 141201;

    public const SETTING_FORMAT = 'format';
    public const SETTING_MAX_YEAR = 'maxYear';
    public const SETTING_MIN_YEAR = 'minYear';
    public const SETTING_EMPTY_OPTION_ENABLED = 'addEmptyOption';
    public const SETTING_EMPTY_OPTION_VALUE = 'emptyOptionValue';
    public const SETTING_EMPTY_OPTION_TEXT = 'emptyOptionText';
    public const DEFAULT_FORMAT = 'dMY';
    public const NAMES_MONTHS_SHORT = 'months_short';
    public const NAMES_MONTHS_LONG = 'months_long';
    public const NAMES_WEEKDAYS_SHORT = 'weekdays_short';
    public const NAMES_WEEKDAYS_LONG = 'weekdays_long';
    public const SETTING_MIN_HOUR = 'minHour';
    public const SETTING_MAX_HOUR = 'maxHour';
    public const SETTING_MIN_MONTH = 'minMonth';
    public const SETTING_MAX_MONTH = 'maxMonth';

    public function getType() : string
    {
        return 'date';
    }

   /**
    * Various options to control the element's display.
    * @var array<string,mixed>
    */
    protected $data = array(
        self::SETTING_FORMAT => self::DEFAULT_FORMAT,
        self::SETTING_MIN_YEAR => 2001,
        self::SETTING_MAX_YEAR => null, // set in the constructor
        self::SETTING_EMPTY_OPTION_ENABLED => false,
        self::SETTING_EMPTY_OPTION_VALUE => '',
        self::SETTING_EMPTY_OPTION_TEXT => '&nbsp;',
        'optionIncrement'  => array('i' => 1, 's' => 1),
        // request #4061: max and min hours (only for 'H' modifier)
        self::SETTING_MIN_HOUR => 0,
        self::SETTING_MAX_HOUR => 23,
        // request #5957: max and min months
        self::SETTING_MIN_MONTH => 1,
        self::SETTING_MAX_MONTH => 12
    );

   /**
    * Language code
    * @var string|NULL
    */
    protected ?string $language = null;

   /**
    * Message provider for option texts
    * @var callable|HTML_QuickForm2_MessageProvider
    */
    protected $messageProvider;
    private bool $initDone;

   /**
    * Class constructor
    *
    * The following keys may appear in $data array:
    *
    * - 'messageProvider': a callback or an instance of a class implementing
    *   HTML_QuickForm2_MessageProvider interface, this will be used to get
    *   localized names of months and weekdays. Default ones will be used if
    *   not given.
    * - 'language': date language, use 'locale' here to display month / weekday
    *   names according to the current locale.
    * - 'format': Format of the date, based on PHP's date() function.
    *   The following characters are currently recognised in format string:
    *   <pre>
    *       D => Short names of days
    *       l => Long names of days
    *       d => Day numbers
    *       M => Short names of months
    *       F => Long names of months
    *       m => Month numbers
    *       Y => Four digit year
    *       y => Two digit year
    *       h => 12-hour format
    *       H => 24 hour format
    *       i => Minutes
    *       s => Seconds
    *       a => am/pm
    *       A => AM/PM
    *   </pre>
    * - 'minYear': Minimum year in year select
    * - 'maxYear': Maximum year in year select
    * - 'addEmptyOption': Should an empty option be added to the top of
    *    each select box?
    * - 'emptyOptionValue': The value passed by the empty option.
    * - 'emptyOptionText': The text displayed for the empty option.
    * - 'optionIncrement': Step to increase the option values by (works for 'i' and 's')
    * - 'minHour': Minimum hour in hour select (only for 24-hour format!)
    * - 'maxHour': Maximum hour in hour select (only for 24-hour format!)
    * - 'minMonth': Minimum month in month select
    * - 'maxMonth': Maximum month in month select
    *
    * @param string       $name       Element name
    * @param string|array $attributes Attributes (either a string or an array)
    * @param array        $data       Element data (label, options and data used for element creation)
    *
    * @throws HTML_QuickForm2_InvalidArgumentException
    */
    public function __construct($name = null, $attributes = null, array $data = array())
    {
        $this->initDone = false;

        if (isset($data['messageProvider'])) {
            $this->setMessageProvider($data['messageProvider']);
        }

        if (isset($data['language'])) {
            $this->setLanguage($data['language']);
        }

        unset($data['messageProvider'], $data['language']);

        // http://pear.php.net/bugs/bug.php?id=18171
        $this->data[self::SETTING_MAX_YEAR] = date('Y');

        parent::__construct($name, $attributes, $data);

        $this->generateSelects();

        $this->initDone = true;
    }

    public function setLanguage(string $lang) : self
    {
        $this->language = $lang;
        return $this;
    }

    /**
     * @param callable|HTML_QuickForm2_MessageProvider|mixed $provider
     * @return $this
     * @throws HTML_QuickForm2_InvalidArgumentException
     */
    public function setMessageProvider($provider) : self
    {
        if (
            is_callable($provider)
            ||
            $provider instanceof HTML_QuickForm2_MessageProvider
        ) {
            $this->messageProvider = $provider;
            return $this;
        }

        throw new HTML_QuickForm2_InvalidArgumentException(
            sprintf(
                "messageProvider: expecting a callback or an implementation of %s",
                HTML_QuickForm2_MessageProvider::class
            ),
            self::ERROR_INVALID_MESSAGE_PROVIDER
        );
    }

    /**
     * @return HTML_QuickForm2_MessageProvider|callable
     */
    public function getMessageProvider()
    {
        if(!isset($this->messageProvider)) {
            if ($this->getLanguage() === 'locale') {
                $this->messageProvider = new HTML_QuickForm2_MessageProvider_Strftime();
            } else {
                $this->messageProvider = HTML_QuickForm2_MessageProvider_Default::getInstance();
            }
        }

        return $this->messageProvider;
    }

    public function getLanguage() : ?string
    {
        return $this->language;
    }

    protected function generateSelects() : void
    {
        if($this->selectsGenerated === true || !$this->initDone) {
            return;
        }

        $this->selectsGenerated = true;

        $backslash = false;
        $separators = array();
        $separator =  '';

        for ($i = 0, $length = strlen($this->data[self::SETTING_FORMAT]); $i < $length; $i++) {
            $sign = $this->data[self::SETTING_FORMAT][$i];
            if ($backslash) {
                $backslash  = false;
                $separator .= $sign;
            } else {
                $loadSelect = true;
                $options    = array();
                switch ($sign) {
                    case 'D':
                        // Sunday is 0 like with 'w' in date()
                        $options = $this->getWeekdayNames(self::NAMES_WEEKDAYS_SHORT);
                        break;
                    case 'l':
                        $options = $this->getWeekdayNames(self::NAMES_WEEKDAYS_LONG);
                        break;
                    case 'd':
                        $options = $this->createOptionList(1, 31);
                        break;
                    case 'M':
                    case 'm':
                    case 'F':
                        $min = $this->getMinMonth();
                        $max = $this->getMaxMonth();
                        $options = $this->createOptionList(
                            $min,
                            $max,
                            $min > $max ? -1 : 1
                        );
                        if ('M' === $sign || 'F' === $sign) {
                            $key   = 'M' === $sign ? self::NAMES_MONTHS_SHORT : self::NAMES_MONTHS_LONG;
                            $names = $this->getMonthNames($key);

                            foreach ($options as $k => $value) {
                                $options[$k] = $names[$k - 1];
                            }
                        }
                        break;
                    case 'Y':
                        $min = $this->getMinYear();
                        $max = $this->getMaxYear();
                        $options = $this->createOptionList(
                            $min,
                            $max,
                            $min > $max? -1: 1
                        );
                        break;
                    case 'y':
                        $min = $this->getMinYear();
                        $max = $this->getMaxYear();
                        $options = $this->createOptionList(
                            $min,
                            $max,
                            $min > $max? -1: 1
                        );
                        array_walk($options, array($this, '_shortYearCallback'));
                        break;
                    case 'h':
                        $options = $this->createOptionList(1, 12);
                        break;
                    case 'g':
                        $options = $this->createOptionList(1, 12);
                        array_walk($options, array($this, '_shortHourCallback'));
                        break;
                    case 'H':
                        $min = $this->getMinHour();
                        $max = $this->getMaxHour();
                        $options = $this->createOptionList(
                            $min,
                            $max,
                            $min > $max ? -1 : 1
                        );
                        break;
                    case 'i':
                        $options = $this->createOptionList(0, 59, $this->data['optionIncrement']['i']);
                        break;
                    case 's':
                        $options = $this->createOptionList(0, 59, $this->data['optionIncrement']['s']);
                        break;
                    case 'a':
                        $options = array('am' => 'am', 'pm' => 'pm');
                        break;
                    case 'A':
                        $options = array('AM' => 'AM', 'PM' => 'PM');
                        break;
                    case 'W':
                        $options = $this->createOptionList(1, 53);
                        break;
                    case '\\':
                        $backslash  = true;
                        $loadSelect = false;
                        break;
                    default:
                        $separator .= (' ' === $sign? '&nbsp;': $sign);
                        $loadSelect = false;
                }

                if ($loadSelect)
                {
                    if (0 < count($this)) {
                        $separators[] = $separator;
                    }
                    $separator = '';

                    // Should we add an empty option to the top of the select?
                    if ($this->hasEmptyOption($sign))
                    {
                        // Using '+' array operator to preserve the keys
                        $options = array($this->getEmptyOptionValue($sign) => $this->getEmptyOptionText($sign)) + $options;
                    }

                    $this->addSelect(
                        $sign,
                        array('id' => self::generateId($this->getName() . '['.$sign.']'))
                        + $this->getAttributes()
                    )
                        ->loadOptions($options);
                }
            }
        }
        $separators[] = $separator . ($backslash? '\\': '');
        $this->setSeparator($separators);
    }

    public function getWeekdayNames(string $key=self::NAMES_WEEKDAYS_LONG) : array
    {
        $provider = $this->getMessageProvider();
        $lang = $this->getLanguage();

        if($provider instanceof HTML_QuickForm2_MessageProvider) {
            return $this->messageProvider->get(array('date', $key), $lang);
        }

        return $provider(array('date', $key), $lang);
    }

    /**
     * @param string $key Either <code>months_short</code> or <code>months_long</code>.
     * @return string[]
     * @throws HTML_QuickForm2_InvalidArgumentException
     * @see self::NAMES_MONTHS_LONG
     * @see self::NAMES_MONTHS_SHORT
     */
    public function getMonthNames(string $key=self::NAMES_MONTHS_LONG) : array
    {
        $provider = $this->getMessageProvider();
        $lang = $this->getLanguage();

        if($provider instanceof HTML_QuickForm2_MessageProvider)
        {
            return $provider->get(array('date', $key), $lang);
        }

        return $provider(array('date', $key), $lang);
    }

    public function hasEmptyOption(string $formatSign) : bool
    {
        if(empty($this->data[self::SETTING_EMPTY_OPTION_ENABLED])) {
            return false;
        }

        if(is_bool($this->data[self::SETTING_EMPTY_OPTION_ENABLED])) {
            return $this->data[self::SETTING_EMPTY_OPTION_ENABLED];
        }

        if(
            is_array($this->data[self::SETTING_EMPTY_OPTION_ENABLED])
            &&
            !empty($this->data[self::SETTING_EMPTY_OPTION_ENABLED][$formatSign])
            &&
            $this->data[self::SETTING_EMPTY_OPTION_ENABLED][$formatSign] === true
        ) {
            return true;
        }

        return false;
    }

    protected function getEmptyOptionValue(string $formatSign) : string
    {
        if(empty($this->data[self::SETTING_EMPTY_OPTION_VALUE])) {
            return '';
        }

        if(is_array($this->data[self::SETTING_EMPTY_OPTION_VALUE])) {
            return $this->data[self::SETTING_EMPTY_OPTION_VALUE][$formatSign] ?? '';
        }

        return (string)$this->data[self::SETTING_EMPTY_OPTION_VALUE];
    }

    protected function getEmptyOptionText(string $formatSign) : string
    {
        if(empty($this->data[self::SETTING_EMPTY_OPTION_TEXT])) {
            return '';
        }

        if(is_array($this->data[self::SETTING_EMPTY_OPTION_TEXT])) {
            return $this->data[self::SETTING_EMPTY_OPTION_TEXT][$formatSign] ?? '';
        }

        return (string)$this->data[self::SETTING_EMPTY_OPTION_TEXT];
    }

    public function setEmptyOptionForAll(string $text, string $value) : self
    {
        return $this->setEmptyOption($text, $value);
    }

    public function setEmptyOptionForFormat(string $formatSign, string $text, string $value) : self
    {
       return $this->setEmptyOption($text, $value, $formatSign);
    }

    public function preRender() : void
    {
        $this->generateSelects();

        parent::preRender();
    }

    public function getElements() : array
    {
        $this->generateSelects();

        return parent::getElements();
    }

    protected function setEmptyOption(string $text, string $value, ?string $formatSign=null) : self
    {
        $this->resetSelects();

        if(empty($formatSign))
        {
            $this->data[self::SETTING_EMPTY_OPTION_ENABLED] = true;
            $this->data[self::SETTING_EMPTY_OPTION_TEXT] = $text;
            $this->data[self::SETTING_EMPTY_OPTION_VALUE] = $value;
            return $this;
        }

        if(!is_array($this->data[self::SETTING_EMPTY_OPTION_ENABLED])) {
            $this->data[self::SETTING_EMPTY_OPTION_ENABLED] = array();
        }

        if(!is_array($this->data[self::SETTING_EMPTY_OPTION_TEXT])) {
            $this->data[self::SETTING_EMPTY_OPTION_TEXT] = array();
        }

        if(!is_array($this->data[self::SETTING_EMPTY_OPTION_VALUE])) {
            $this->data[self::SETTING_EMPTY_OPTION_VALUE] = array();
        }

        $this->data[self::SETTING_EMPTY_OPTION_ENABLED][$formatSign] = true;
        $this->data[self::SETTING_EMPTY_OPTION_TEXT][$formatSign] = $text;
        $this->data[self::SETTING_EMPTY_OPTION_VALUE][$formatSign] = $value;

        return $this;
    }

    /**
     * Callback for creating two-digit year list, formerly via create_function()
     *
     * @param string $v
     */
    private function _shortYearCallback(string &$v): void
    {
        $v = substr($v,-2);
    }

    /**
     * Callback for creating hour list without leading zeroes, formerly via create_function()
     *
     * @param string|int $v
     */
    private function _shortHourCallback(&$v): void
    {
        $v = (int)$v;
    }

   /**
    * Creates an option list containing the numbers from the start number to the end, inclusive
    *
    * @param int $start The start number
    * @param int $end   The end number
    * @param int $step  Increment by this value
    *
    * @return   array   An array of numeric options.
    */
    protected function createOptionList(int $start, int $end, int $step = 1) : array
    {
        for ($i = $start, $options = array(); $start > $end? $i >= $end: $i <= $end; $i += $step) {
            $options[$i] = sprintf('%02d', $i);
        }
        return $options;
    }

   /**
    * Trims leading zeros from the (numeric) string
    *
    * @param string $str A numeric string, possibly with leading zeros
    *
    * @return   string  String with leading zeros removed
    */
    protected function trimLeadingZeros(string $str) : string
    {
        if (strcmp($str, $this->data[self::SETTING_EMPTY_OPTION_VALUE]) === 0) {
            return $str;
        }

        $trimmed = ltrim($str, '0');

        if(!empty($trimmed)) {
            return $trimmed;
        }

        return '0';
    }


   /**
    * Tries to convert the given value to a usable date before setting the
    * element value
    *
    * @param int|string|array|DateTime|DateTimeInterface $value A timestamp, a DateTime object,
    *   a string compatible with <code>strtotime()</code> or an array that fits the element names.
    *
    * @return $this
    */
    public function setValue($value) : self
    {
        if (empty($value)) {
            return parent::setValue(array());
        }

        if (is_array($value)) {
            return parent::setValue(array_map(array($this, 'trimLeadingZeros'), $value));
        }

        if($value instanceof DateTimeInterface)
        {
            $arr = explode('-', $value->format('w-j-n-Y-g-G-i-s-a-A-W'));
        }
        else if(is_scalar($value))
        {
            if (!is_numeric($value))
            {
                $timestamp = strtotime($value);

                if($timestamp === false)
                {
                    return parent::setValue(array());
                }

                $value = $timestamp;
            }
                // might be a unix epoch, then we fill all possible values
            $arr = explode('-', date('w-j-n-Y-g-G-i-s-a-A-W', (int)$value));
        }
        else
        {
            return $this;
        }

        $value = array(
            'D' => $arr[0],
            'l' => $arr[0],
            'd' => $arr[1],
            'M' => $arr[2],
            'm' => $arr[2],
            'F' => $arr[2],
            'Y' => $arr[3],
            'y' => $arr[3],
            'h' => $arr[4],
            'g' => $arr[4],
            'H' => $arr[5],
            'i' => $this->trimLeadingZeros($arr[6]),
            's' => $this->trimLeadingZeros($arr[7]),
            'a' => $arr[8],
            'A' => $arr[9],
            'W' => $this->trimLeadingZeros($arr[10])
        );

        return parent::setValue($value);
    }

   /**
    * Called when the element needs to update its value from form's data sources
    *
    * Since the date element also accepts a timestamp as value, the default
    * group behavior is changed.
    */
    protected function updateValue() : void
    {
        $name = $this->getName();

        foreach ($this->getDataSources() as $ds)
        {
            if (
                null !== ($value = $ds->getValue($name))
                ||
                ($ds instanceof HTML_QuickForm2_DataSource_NullAware && $ds->hasValue($name))
            ) {
                $this->setValue($value);
                return;
            }
        }

        parent::updateValue();
    }

    private bool $selectsGenerated = false;

    protected function resetSelects() : void
    {
        $children = $this->getElements();
        foreach($children as $child) {
            $this->removeChild($child);
        }

        $this->selectsGenerated = false;
    }

    public function setFormat(string $format) : self
    {
        $this->resetSelects();

        return $this->setDataKey(self::SETTING_FORMAT, $format);
    }

    public function getFormat() : string
    {
        return $this->getDataKeyString(self::SETTING_FORMAT);
    }

    public function setMaxYear(int $year) : self
    {
        $this->resetSelects();

        return $this->setDataKey(self::SETTING_MAX_YEAR, $year);
    }

    public function getMaxYear() : int
    {
        return $this->getDataKeyInt(self::SETTING_MAX_YEAR);
    }

    public function setMinYear(int $year) : self
    {
        $this->resetSelects();

        return $this->setDataKey(self::SETTING_MIN_YEAR, $year);
    }

    public function getMinYear() : int
    {
        return $this->getDataKeyInt(self::SETTING_MIN_YEAR);
    }

    public function setMinHour(int $hour) : self
    {
        return $this->setDataKey(self::SETTING_MIN_HOUR, $hour);
    }

    public function getMinHour() : int
    {
        return $this->getDataKeyInt(self::SETTING_MIN_HOUR);
    }

    public function setMaxHour(int $hour) : self
    {
        return $this->setDataKey(self::SETTING_MAX_HOUR, $hour);
    }

    public function getMaxHour() : int
    {
        return $this->getDataKeyInt(self::SETTING_MAX_HOUR);
    }

    public function setMinMonth(int $month) : self
    {
        return $this->setDataKey(self::SETTING_MIN_MONTH, $month);
    }

    public function getMinMonth() : int
    {
        return $this->getDataKeyInt(self::SETTING_MIN_MONTH);
    }

    public function setMaxMonth(int $month) : self
    {
        return $this->setDataKey(self::SETTING_MAX_MONTH, $month);
    }

    public function getMaxMonth() : int
    {
        return $this->getDataKeyInt(self::SETTING_MAX_MONTH);
    }
}
