<?php

namespace GetLaminas\Security;

use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use Psr\Container\ContainerInterface;

class AdvisoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new Advisory(
            new Parser(null, new CommonMarkParser())
        );
    }
}
