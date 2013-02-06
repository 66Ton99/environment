<?php
namespace Environment\Tester;

use Environment\Tester;
use Environment\TesterOutput;

class PhpIni extends Tester
{
    protected $defaults = array(
        'option' => 'php ini option',
        'expected' => 'php ini value',
        'regex' => null,
    );

    public function test()
    {
        $value = ini_get($this->get('option'));
        $this->set('output', new TesterOutput($value));
        return $this->testExpected($value) || $this->testRegex($value);
    }


    public function testExpected($text)
    {
        return !is_null($this->get('expected')) && $this->get('expected') == $text;
    }

    public function testRegex($text)
    {
        return !is_null($this->get('regex')) && preg_match($this->get('regex'), $text);
    }

}
