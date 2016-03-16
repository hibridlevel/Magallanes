<?php
namespace Mage\Command;

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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BaseCommand
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    const CODE_SUCCESS = 0;
    const CODE_ERROR   = 1;

    /**
 * @var SymfonyStyle
*/
    private $io;

    public function __construct($container = null)
    {
        parent::__construct($container);
    }

    /**
     * initialize the commands input and output
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * prints success message to console and returns success code
     *
     * @param  $message string the message to display on the console
     * @return int the success status code
     */
    protected function success($message = null)
    {
        if (!is_null($message)) {
            $this->io->success($message);
        }

        return self::CODE_SUCCESS;
    }

    /**
     * prints error message to console and returns default error code
     *
     * @param  $message string the message to display on the console
     * @return int the error status code
     */
    protected function error($message)
    {
        $this->io->error($message);
        return self::CODE_ERROR;
    }

    protected function getIO()
    {
        return $this->io;
    }
}
