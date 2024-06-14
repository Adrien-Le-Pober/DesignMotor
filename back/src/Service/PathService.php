<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

class PathService
{
    public function __construct(private KernelInterface $kernel)
    { }

    public function getImagePath(): string
    {
        $projectDir = $this->kernel->getProjectDir();
        return $projectDir . '/public/images/';
    }

    public function getVideoPath(): string
    {
        $projectDir = $this->kernel->getProjectDir();
        return $projectDir . '/public/videos/';
    }
}