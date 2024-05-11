<?php

namespace App\Proxy;

class VideoProxy
{
    private ?Video $video = null;

    public function loadVideo(string $videoPath)
    {
        if (!$this->video) {
            $this->video = new Video();
        }
        // Charger la vidéo en utilisant le Proxy
        return $this->video->loadVideo($videoPath);
    }
}