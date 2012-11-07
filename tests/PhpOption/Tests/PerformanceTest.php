<?php

namespace PhpOption\Tests;

/**
 * @group performance
 */
class PerformanceTest extends \PHPUnit_Framework_TestCase
{
    private $traditionalRepo;
    private $phpOptionRepo;

    public function testSomeCase()
    {
        $traditionalTime = microtime(true);
        for ($i=0; $i<10000; $i++) {
            if (null === $rs = $this->traditionalRepo->findMaybe(true)) {
                $rs = new \stdClass();
            }
        }
        $traditionalTime = microtime(true) - $traditionalTime;

        $phpOptionTime = microtime(true);
        for ($i=0; $i<10000; $i++) {
            $rs = $this->phpOptionRepo->findMaybe(true)->getOrElse(new \stdClass);
        }
        $phpOptionTime = microtime(true) - $phpOptionTime;

        $overheadPerInvocation = ($phpOptionTime - $traditionalTime) / 10000;
        printf("Overhead per invocation (some case): %.9fs\n", $overheadPerInvocation);
    }

    public function testNoneCase()
    {
        $traditionalTime = microtime(true);
        for ($i=0; $i<10000; $i++) {
            if (null === $rs = $this->traditionalRepo->findMaybe(false)) {
                $rs = new \stdClass();
            }
        }
        $traditionalTime = microtime(true) - $traditionalTime;

        $phpOptionTime = microtime(true);
        for ($i=0; $i<10000; $i++) {
            $rs = $this->phpOptionRepo->findMaybe(false)->getOrElse(new \stdClass);
        }
        $phpOptionTime = microtime(true) - $phpOptionTime;

        $overheadPerInvocation = ($phpOptionTime - $traditionalTime) / 10000;
        printf("Overhead per invocation (none case): %.9fs\n", $overheadPerInvocation);
    }

    protected function setUp()
    {
        $this->traditionalRepo = new TraditionalRepo();
        $this->phpOptionRepo = new PhpOptionRepo();
    }
}

class TraditionalRepo
{
    public function findMaybe($success)
    {
        if ($success) {
            return new \stdClass;
        }

        return null;
    }
}

class PhpOptionRepo
{
    public function findMaybe($success)
    {
        if ($success) {
            return new \PhpOption\Some(new \stdClass);
        }

        return \PhpOption\None::create();
    }
}