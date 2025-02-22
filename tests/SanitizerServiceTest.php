<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/services/SanitizerService.php';

use PHPUnit\Framework\TestCase;

class SanitizerServiceTest extends TestCase
{
    private $sanitizer;
    private $pdo;
    
    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class); // Note the leading backslash
        $this->sanitizer = new SanitizerService($this->pdo);
    }
    
    public function testBasicTextSanitization()
    {
        $input = "<script>alert('xss')</script>Hello & World";
        $expected = "Hello &amp; World";
        $this->assertEquals($expected, $this->sanitizer->sanitizeInput($input));
    }
    
    public function testHtmlSanitization()
    {
        $input = '<p>Valid</p><script>alert("bad")</script>';
        $expected = '<p>Valid</p>';
        $this->assertEquals($expected, $this->sanitizer->sanitizeInput($input, 'html'));
    }
    
    public function testJavaScriptContextSanitization()
    {
        $input = 'Hello"World';
        $expected = '"Hello\"World"';
        $this->assertEquals($expected, $this->sanitizer->sanitizeInput($input, 'js'));
    }
    
    public function testSqlInjectionPrevention()
    {
        $this->pdo->expects($this->once())
            ->method('quote')
            ->willReturn("'sanitized_value'");
        
        $input = "'; DROP TABLE users; --";
        $this->assertEquals("'sanitized_value'", $this->sanitizer->sanitizeInput($input, 'sql'));
    }
    
    public function testArraySanitization()
    {
        $input = [
            "<script>alert('xss')</script>Hello",
            "<p>Test</p>"
        ];
        $expected = [
            "Hello",
            "<p>Test</p>"
        ];
        $this->assertEquals($expected, $this->sanitizer->sanitizeInput($input, 'html'));
    }
    
    public function testNonStringInput()
    {
        $input = 42;
        $this->assertEquals(42, $this->sanitizer->sanitizeInput($input));
    }
    
    public function testFileValidation()
    {
        $validFile = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'size' => 1024
        ];
        
        $this->assertTrue($this->sanitizer->validateFile($validFile));
        
        $invalidFile = [
            'name' => 'test.php',
            'type' => 'application/x-php',
            'size' => 1024
        ];
        
        $this->assertFalse($this->sanitizer->validateFile($invalidFile));
    }
}