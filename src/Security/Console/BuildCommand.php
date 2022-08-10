<?php

declare(strict_types=1);

namespace GetLaminas\Security\Console;

use App\FrontMatter\Parser;
use GetLaminas\Security\Advisory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function file_exists;
use function unlink;

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

        // Instantiating builds the cache file
        new Advisory(new Parser());

        $io->success('DONE building security advisories');

        return 0;
    }
}
