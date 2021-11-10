<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ChangePasswordUserCommand extends AbstractUserCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('app:user:change-password')
            ->setDescription('Change password of a user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password')
            ]);

        parent::configure();
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email    = $input->getArgument('email');
        $password = $input->getArgument('password');

        $this->userUtil->changePassword($email, $password);

        $output->writeln(sprintf('Chaned password for user <comment>%s</comment>', $email));

        return Command::SUCCESS;
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please give an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }
                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please enter the new password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }
                if (strlen($password) < 6) {
                    throw new \Exception('Password has to be at least 6 characters long');
                }
                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function getHelpText(): string
    {
        return <<<EOT
The <info>app:user:change-password</info> command changes password of a user:
  <info>php bin/console app:user:change-password mail@example.com</info>
This interactive shell will ask you for an password.
You can alternatively specify the email and password as the first and second arguments:
  <info>php bin/console app:user:change-password mail@example.com mypassword</info>
EOT;
    }
}
