#!/usr/bin/env php
<?php
/**
 * Simple Test Runner
 * 
 * Usage: php tests/run-tests.php
 * 
 * @package ZargarAccounting
 * @subpackage Tests
 */

require_once __DIR__ . '/bootstrap.php';

// ANSI colors
define('COLOR_GREEN', "\033[32m");
define('COLOR_RED', "\033[31m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_RESET', "\033[0m");

class SimpleTestRunner {
    private $passed = 0;
    private $failed = 0;
    private $tests = [];
    
    public function test($name, $callback) {
        $this->tests[] = ['name' => $name, 'callback' => $callback];
    }
    
    public function run() {
        echo COLOR_BLUE . "\n🧪 Running Zargar Accounting Tests...\n" . COLOR_RESET;
        echo str_repeat("=", 50) . "\n\n";
        
        foreach ($this->tests as $test) {
            $this->runTest($test['name'], $test['callback']);
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo COLOR_BLUE . "📊 Test Results:\n" . COLOR_RESET;
        echo COLOR_GREEN . "✓ Passed: {$this->passed}\n" . COLOR_RESET;
        echo COLOR_RED . "✗ Failed: {$this->failed}\n" . COLOR_RESET;
        echo str_repeat("=", 50) . "\n";
        
        return $this->failed === 0;
    }
    
    private function runTest($name, $callback) {
        try {
            $callback();
            $this->passed++;
            echo COLOR_GREEN . "✓ " . COLOR_RESET . $name . "\n";
        } catch (Exception $e) {
            $this->failed++;
            echo COLOR_RED . "✗ " . COLOR_RESET . $name . "\n";
            echo COLOR_RED . "  Error: " . $e->getMessage() . COLOR_RESET . "\n";
        }
    }
    
    public function assertEquals($expected, $actual, $message = '') {
        if ($expected !== $actual) {
            throw new Exception($message ?: "Expected " . var_export($expected, true) . " but got " . var_export($actual, true));
        }
    }
    
    public function assertTrue($value, $message = '') {
        if ($value !== true) {
            throw new Exception($message ?: "Expected true but got " . var_export($value, true));
        }
    }
    
    public function assertFalse($value, $message = '') {
        if ($value !== false) {
            throw new Exception($message ?: "Expected false but got " . var_export($value, true));
        }
    }
    
    public function assertNotNull($value, $message = '') {
        if ($value === null) {
            throw new Exception($message ?: "Expected non-null value");
        }
    }
    
    public function assertInstanceOf($class, $object, $message = '') {
        if (!($object instanceof $class)) {
            throw new Exception($message ?: "Expected instance of {$class}");
        }
    }
}

$runner = new SimpleTestRunner();

// ============================================================================
// Logger Tests
// ============================================================================

$runner->test('Logger: Singleton instance', function() use ($runner) {
    $logger1 = ZargarAccounting\Logger\Logger::getInstance();
    $logger2 = ZargarAccounting\Logger\Logger::getInstance();
    $runner->assertInstanceOf(ZargarAccounting\Logger\Logger::class, $logger1);
    $runner->assertEquals($logger1, $logger2, 'Should return same instance');
});

$runner->test('Logger: Can log INFO message', function() use ($runner) {
    $logger = ZargarAccounting\Logger\Logger::getInstance();
    $logger->info('Test info message');
    $runner->assertTrue(true); // If no exception, test passes
});

$runner->test('Logger: Can log ERROR message', function() use ($runner) {
    $logger = ZargarAccounting\Logger\Logger::getInstance();
    $logger->error('Test error message');
    $runner->assertTrue(true);
});

$runner->test('Logger: Can log with context', function() use ($runner) {
    $logger = ZargarAccounting\Logger\Logger::getInstance();
    $logger->warning('Test warning', ['user_id' => 123, 'action' => 'test']);
    $runner->assertTrue(true);
});

// ============================================================================
// AdvancedLogger Tests
// ============================================================================

$runner->test('AdvancedLogger: Singleton instance', function() use ($runner) {
    $logger1 = ZargarAccounting\Logger\AdvancedLogger::getInstance();
    $logger2 = ZargarAccounting\Logger\AdvancedLogger::getInstance();
    $runner->assertInstanceOf(ZargarAccounting\Logger\AdvancedLogger::class, $logger1);
    $runner->assertEquals($logger1, $logger2);
});

$runner->test('AdvancedLogger: Can log product events', function() use ($runner) {
    $logger = ZargarAccounting\Logger\AdvancedLogger::getInstance();
    $logger->logProduct('Product sync started', ['product_id' => 123]);
    $runner->assertTrue(true);
});

$runner->test('AdvancedLogger: Can log sales events', function() use ($runner) {
    $logger = ZargarAccounting\Logger\AdvancedLogger::getInstance();
    $logger->logSales('Sale completed', ['order_id' => 456]);
    $runner->assertTrue(true);
});

$runner->test('AdvancedLogger: Can log price events', function() use ($runner) {
    $logger = ZargarAccounting\Logger\AdvancedLogger::getInstance();
    $logger->logPrice('Price updated', ['product_id' => 789, 'new_price' => 100]);
    $runner->assertTrue(true);
});

$runner->test('AdvancedLogger: Can log errors', function() use ($runner) {
    $logger = ZargarAccounting\Logger\AdvancedLogger::getInstance();
    $logger->logError('API connection failed', ['error_code' => 500]);
    $runner->assertTrue(true);
});

$runner->test('AdvancedLogger: Has correct log type constants', function() use ($runner) {
    $runner->assertEquals('product', ZargarAccounting\Logger\AdvancedLogger::TYPE_PRODUCT);
    $runner->assertEquals('sales', ZargarAccounting\Logger\AdvancedLogger::TYPE_SALES);
    $runner->assertEquals('price', ZargarAccounting\Logger\AdvancedLogger::TYPE_PRICE);
    $runner->assertEquals('error', ZargarAccounting\Logger\AdvancedLogger::TYPE_ERROR);
});

// ============================================================================
// LoggerAjax Tests
// ============================================================================

$runner->test('LoggerAjax: Singleton instance', function() use ($runner) {
    $ajax1 = ZargarAccounting\Logger\LoggerAjax::getInstance();
    $ajax2 = ZargarAccounting\Logger\LoggerAjax::getInstance();
    $runner->assertInstanceOf(ZargarAccounting\Logger\LoggerAjax::class, $ajax1);
    $runner->assertEquals($ajax1, $ajax2);
});

// ============================================================================
// BladeRenderer Tests
// ============================================================================

$runner->test('BladeRenderer: Singleton instance', function() use ($runner) {
    $blade1 = ZargarAccounting\Core\BladeRenderer::getInstance();
    $blade2 = ZargarAccounting\Core\BladeRenderer::getInstance();
    $runner->assertInstanceOf(ZargarAccounting\Core\BladeRenderer::class, $blade1);
    $runner->assertEquals($blade1, $blade2);
});

// ============================================================================
// Integration Tests
// ============================================================================

$runner->test('Integration: All main classes load correctly', function() use ($runner) {
    $logger = ZargarAccounting\Logger\Logger::getInstance();
    $advancedLogger = ZargarAccounting\Logger\AdvancedLogger::getInstance();
    $loggerAjax = ZargarAccounting\Logger\LoggerAjax::getInstance();
    $blade = ZargarAccounting\Core\BladeRenderer::getInstance();
    
    $runner->assertNotNull($logger);
    $runner->assertNotNull($advancedLogger);
    $runner->assertNotNull($loggerAjax);
    $runner->assertNotNull($blade);
});

// Run all tests
$success = $runner->run();

exit($success ? 0 : 1);
