<?php

namespace App\Trait;

trait Base64ImageTrait {
    private function getBase64Image(string $imagePath): string {
        $imageContents = file_get_contents($imagePath);
        $base64Image = base64_encode($imageContents);
        return 'data:image/jpeg;base64,' . $base64Image;
    }
}