<?php

namespace App\Controllers;

class AppController
{
    protected function render(?string $template = null, array $variables = []): void
    {
        $templatePath = __DIR__ . '/../../public/views/' . $template . '.html.php';
        $output = 'File not found';
                
        if (file_exists($templatePath)) {
            extract($variables);
            
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }
        
        echo $output;
    }
}