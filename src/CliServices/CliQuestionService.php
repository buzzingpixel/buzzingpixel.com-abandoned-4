<?php

declare(strict_types=1);

namespace App\CliServices;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use function is_string;

class CliQuestionService
{
    /** @var QuestionHelper */
    private $questionHelper;
    /** @var InputInterface */
    private $consoleInput;
    /** @var OutputInterface */
    private $consoleOutput;

    public function __construct(
        QuestionHelper $questionHelper,
        InputInterface $consoleInput,
        OutputInterface $consoleOutput
    ) {
        $this->questionHelper = $questionHelper;
        $this->consoleInput   = $consoleInput;
        $this->consoleOutput  = $consoleOutput;
    }

    public function ask(
        string $question,
        bool $required = true,
        bool $hidden = false
    ) : string {
        $questionEntity = new Question($question);

        if ($hidden) {
            $questionEntity->setHidden(true);
        }

        $val = '';

        while ($val === '') {
            /** @var string|null $val */
            $val = $this->questionHelper->ask(
                $this->consoleInput,
                $this->consoleOutput,
                $questionEntity
            );

            if (! $required) {
                return is_string($val) ? $val : '';
            }

            if ($val !== '' && $val !== null) {
                continue;
            }

            $this->consoleOutput->writeln(
                '<fg=red>You must provide a value</>'
            );
        }

        return is_string($val) ? $val : '';
    }
}
