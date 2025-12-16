<?php
/**
 * LoggerAjax Test
 * 
 * @package ZargarAccounting
 * @subpackage Tests
 */

use PHPUnit\Framework\TestCase;
use ZargarAccounting\Logger\LoggerAjax;

class LoggerAjaxTest extends TestCase {
    private $ajax;
    
    protected function setUp(): void {
        $this->ajax = LoggerAjax::getInstance();
    }
    
    public function testSingletonInstance() {
        $ajax1 = LoggerAjax::getInstance();
        $ajax2 = LoggerAjax::getInstance();
        
        $this->assertSame($ajax1, $ajax2, 'Should return same instance');
        $this->assertInstanceOf(LoggerAjax::class, $ajax1);
    }
    
    public function testRegisterHooksMethodExists() {
        // Just verify the method exists
        $this->assertTrue(method_exists($this->ajax, 'registerHooks'));
    }
}
