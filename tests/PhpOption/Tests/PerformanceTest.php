<?php

namespace PhpOption\Tests;

use PhpOption\None;
use PhpOption\Some;
use PHPUnit\Framework\TestCase;

/**
 * @group performance
 */
class PerformanceTest extends TestCase
{
    private $traditionalRepo;
    private $phpOptionRepo;

    /**
     * @before
     */
    public function setUpTests()
    {
        $this->traditionalRepo = new TraditionalRepo();
        $this->phpOptionRepo = new PhpOptionRepo();
    }

    public function testSomeCase()
    {
        $traditionalTime = microtime(true);
        for ($i = 0; $i < 10000; $i++) {
            if (null === $rs = $this->traditionalRepo->findMaybe(true)) {
                $rs = new \stdClass();
            }
        }
        $traditionalTime = microtime(true) - $traditionalTime;

        $phpOptionTime = microtime(true);
        for ($i = 0; $i < 10000; $i++) {
            $rs = $this->phpOptionRepo->findMaybe(true)->getOrElse(new \stdClass());
        }
        $phpOptionTime = microtime(true) - $phpOptionTime;

        $overheadPerInvocation = ($phpOptionTime - $traditionalTime) / 10000;
        printf("Overhead per invocation (some case): %.9fs\n", $overheadPerInvocation);
    }

    public function testNoneCase()
    {
        $traditionalTime = microtime(true);
        for ($i = 0; $i < 10000; $i++) {
            if (null === $rs = $this->traditionalRepo->findMaybe(false)) {
                $rs = new \stdClass();
            }
        }
        $traditionalTime = microtime(true) - $traditionalTime;

        $phpOptionTime = microtime(true);
        for ($i = 0; $i < 10000; $i++) {
            $rs = $this->phpOptionRepo->findMaybe(false)->getOrElse(new \stdClass());
        }
        $phpOptionTime = microtime(true) - $phpOptionTime;

        $overheadPerInvocation = ($phpOptionTime - $traditionalTime) / 10000;
        printf("Overhead per invocation (none case): %.9fs\n", $overheadPerInvocation);
    }
}

class TraditionalRepo
{
    public function findMaybe($success)
    {
        if ($success) {
            return new \stdClass();
        }
    }
}

class PhpOptionRepo
{
    public function findMaybe($success)
    {
        if ($success) {
            return new Some(new \stdClass());
        }

        return None::create();
    }
}
