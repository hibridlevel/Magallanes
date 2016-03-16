<?php
namespace MageTest\Command\BuiltIn;

/*
 * (c) 2011-2015 Andrés Montañez <andres@andresmontanez.com>
 * (c) 2016 by Cyberhouse GmbH <office@cyberhouse.at>
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the MIT License (MIT)
 *
 * For the full copyright and license information see
 * <https://opensource.org/licenses/MIT>
 */

use Mage\Command\BuiltIn\UnlockCommand;
use MageTest\TestHelper\BaseTest;
use phpmock\MockBuilder;

/**
 * Class UnlockCommandTest
 * @coversDefaultClass Mage\Command\BuiltIn\UnlockCommand
 * @uses Mage\Command\AbstractCommand
 * @uses Mage\Console
 * @uses Mage\Console\Colors
 */
class UnlockCommandTest extends BaseTest
{
    /**
     * @var UnlockCommand
     */
    private $unlockCommand;

    public static $isUnlinkCalled;
    public static $fileExistsResult;
    public static $isFileExists;

    public function runProvider()
    {
        return [
            'happy_path' => [
                'file_exists' => true,
            ],
            'file_not_exists' => [
                'file_exsits' => false,
            ],
        ];
    }

    /**
     * @before
     */
    public function before()
    {
        $this->unlockCommand = new UnlockCommand();

        self::$isUnlinkCalled   = false;
        self::$fileExistsResult = false;
        self::$isFileExists     = false;

        $mockBuilder    = new MockBuilder();
        $fileExistsMock = $mockBuilder
            ->setName('file_exists')
            ->setNamespace('Mage\Command\BuiltIn')
            ->setFunction(
                function ($filePath) {
                    UnlockCommandTest::$fileExistsResult = $filePath;
                    return UnlockCommandTest::$isFileExists;
                }
            )
            ->build();
        $unlinkMock = $mockBuilder
            ->setName('unlink')
            ->setNamespace('Mage\Command\BuiltIn')
            ->setFunction(
                function () {
                    UnlockCommandTest::$isUnlinkCalled = true;
                }
            )
            ->build();
        $getCwdMock = $mockBuilder
            ->setNamespace('Mage\Command\BuiltIn')
            ->setName('getcwd')
            ->setFunction(
                function () {
                    return '';
                }
            )
            ->build();

        $fileExistsMock->disable();
        $unlinkMock->disable();
        $getCwdMock->disable();

        $fileExistsMock->enable();
        $unlinkMock->enable();
        $getCwdMock->enable();

        $configMock = $this->getMock('Mage\Config');
        $configMock->expects($this->atLeastOnce())
            ->method('getEnvironment')
            ->willReturn('production');
        $this->unlockCommand->setConfig($configMock);

        $this->setUpConsoleStatics();
    }

    /**
     * @covers ::run
     * @dataProvider runProvider
     */
    public function testRun($fileExists)
    {
        $expectedOutput = "\tUnlocked deployment to production environment\n\n";
        $this->expectOutputString($expectedOutput);
        $expectedLockFilePath = '/.mage/production.lock';

        self::$isFileExists = $fileExists;

        $actualExitCode   = $this->unlockCommand->run();
        $expectedExitCode = 0;

        $this->assertEquals(self::$isUnlinkCalled, $fileExists);
        $this->assertEquals($expectedExitCode, $actualExitCode);
        $this->assertEquals($expectedLockFilePath, self::$fileExistsResult);
    }
}
