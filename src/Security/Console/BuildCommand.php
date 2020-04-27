<?php

namespace GetLaminas\Security\Console;

use GetLaminas\Security\Advisory;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Table\TableExtension;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BuildCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('security:build');
        $this->setDescription('Build security advisories from markdown+yaml');
        $this->setHelp(
            'Builds all security advisories from their markdown plus YAML frontmatter.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Building Security Advisories');

        $io->writeln('<info>Removing old cache file</info>');
        $path = __DIR__ . '/../../../var/advisories.php';
        if (file_exists($path)) {
            unlink($path);
        }

        $io->writeln('<info>Building advisory list</info>');
        $env = Environment::createCommonMarkEnvironment();
        $env->addExtension(new TableExtension());

        // Instantiating builds the cache file
        $advisories = new Advisory(
            new Parser(null, new CommonMarkParser(new CommonMarkConverter([], $env)))
        );

        $io->success('DONE building security advisories');

        return 0;
    }
}
