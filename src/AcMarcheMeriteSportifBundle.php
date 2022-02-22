<?php

namespace AcMarche\MeriteSportif;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AcMarcheMeriteSportifBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

}
