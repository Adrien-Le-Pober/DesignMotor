<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

class AssetsService
{
    public function __construct(private KernelInterface $kernel)
    { }

    public function getImagePath(): string
    {
        $projectDir = $this->kernel->getProjectDir();
        return $projectDir . '/assets/images/';
    }
}