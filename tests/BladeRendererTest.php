<?php
/**
 * BladeRenderer Test
 * 
 * @package ZargarAccounting
 * @subpackage Tests
 */

use PHPUnit\Framework\TestCase;
use ZargarAccounting\Core\BladeRenderer;

class BladeRendererTest extends TestCase {
    private $blade;
    
    protected function setUp(): void {
        $this->blade = BladeRenderer::getInstance();
    }
    
    public function testSingletonInstance() {
        $blade1 = BladeRenderer::getInstance();
        $blade2 = BladeRenderer::getInstance();
        
        $this->assertSame($blade1, $blade2, 'Should return same instance');
        $this->assertInstanceOf(BladeRenderer::class, $blade1);
    }
}
