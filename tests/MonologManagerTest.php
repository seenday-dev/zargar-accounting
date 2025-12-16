<?php
/**
 * MonologManager Test
 * 
 * @package ZargarAccounting
 * @subpackage Tests
 */

use PHPUnit\Framework\TestCase;
use ZargarAccounting\Logger\MonologManager;

class MonologManagerTest extends TestCase {
    private $logger;
    
    protected function setUp(): void {
        $this->logger = MonologManager::getInstance();
    }
    
    public function testSingletonInstance() {
        $logger1 = MonologManager::getInstance();
        $logger2 = MonologManager::getInstance();
        
        $this->assertSame($logger1, $logger2, 'Should return same instance');
        $this->assertInstanceOf(MonologManager::class, $logger1);
    }
    
    public function testProductLogging() {
        $this->logger->product('Test product log', ['test' => 'data']);
        $this->assertTrue(true); // If no exception, test passes
    }
    
    public function testProductErrorLogging() {
        $this->logger->productError('Test product error', ['error' => 'test']);
        $this->assertTrue(true);
    }
    
    public function testProductWarningLogging() {
        $this->logger->productWarning('Test product warning', ['warning' => 'test']);
        $this->assertTrue(true);
    }
    
    public function testSalesLogging() {
        $this->logger->sales('Test sales log', ['order_id' => 123]);
        $this->assertTrue(true);
    }
    
    public function testSalesErrorLogging() {
        $this->logger->salesError('Test sales error', ['error' => 'test']);
        $this->assertTrue(true);
    }
    
    public function testPriceLogging() {
        $this->logger->price('Test price log', ['price' => 100]);
        $this->assertTrue(true);
    }
    
    public function testPriceErrorLogging() {
        $this->logger->priceError('Test price error', ['error' => 'test']);
        $this->assertTrue(true);
    }
    
    public function testErrorLogging() {
        $this->logger->error('Test error', ['error_code' => 500]);
        $this->assertTrue(true);
    }
    
    public function testCriticalLogging() {
        $this->logger->critical('Test critical', ['critical' => 'issue']);
        $this->assertTrue(true);
    }
    
    public function testInfoLogging() {
        $this->logger->info('Test info', ['info' => 'data']);
        $this->assertTrue(true);
    }
    
    public function testDebugLogging() {
        $this->logger->debug('Test debug', ['debug' => 'data']);
        $this->assertTrue(true);
    }
    
    public function testWarningLogging() {
        $this->logger->warning('Test warning', ['warning' => 'data']);
        $this->assertTrue(true);
    }
    
    public function testGetLoggerWithValidChannel() {
        $productLogger = $this->logger->getLogger(MonologManager::CHANNEL_PRODUCT);
        $this->assertInstanceOf(\Monolog\Logger::class, $productLogger);
    }
    
    public function testGetLoggerWithInvalidChannel() {
        $this->expectException(\InvalidArgumentException::class);
        $this->logger->getLogger('invalid_channel');
    }
    
    public function testGetRecentLogs() {
        // Log something first
        $this->logger->product('Test for recent logs');
        
        $logs = $this->logger->getRecentLogs(MonologManager::CHANNEL_PRODUCT, 10);
        $this->assertIsArray($logs);
    }
    
    public function testGetStats() {
        $stats = $this->logger->getStats(MonologManager::CHANNEL_PRODUCT);
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('size', $stats);
        $this->assertArrayHasKey('last_modified', $stats);
    }
    
    public function testClearLogs() {
        // Log something first
        $this->logger->product('Test for clear');
        
        $deleted = $this->logger->clearLogs(MonologManager::CHANNEL_PRODUCT);
        $this->assertIsInt($deleted);
    }
    
    public function testChannelConstants() {
        $this->assertEquals('product', MonologManager::CHANNEL_PRODUCT);
        $this->assertEquals('sales', MonologManager::CHANNEL_SALES);
        $this->assertEquals('price', MonologManager::CHANNEL_PRICE);
        $this->assertEquals('error', MonologManager::CHANNEL_ERROR);
        $this->assertEquals('general', MonologManager::CHANNEL_GENERAL);
    }
}
