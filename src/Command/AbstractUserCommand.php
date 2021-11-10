<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use App\Service\UserCommandUtil;

abstract class AbstractUserCommand extends Command
{
    protected $userUtil;

    public function __construct(UserCommandUtil $userUtil)
    {
        $this->userUtil = $userUtil;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setHelp($this->getHelpText());
    }

    abstract protected function getHelpText(): string;
}
