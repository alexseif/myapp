<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/documentation')]
class DocumentationController extends AbstractController
{
    /**
     * Serve documentation files.
     *
     * @param Request $request The current request
     * @param string $path The requested file path
     * @return Response
     * @throws AccessDeniedHttpException If the file is not in the allowed directory
     */
    #[Route('/{path}', name: 'app_documentation', requirements: ['path' => '.+'], defaults: ['path' => 'index.html'])]
    public function serveDocumentation(Request $request, string $path): Response
    {
        $docsDir = $this->getParameter('kernel.project_dir') . '/docs/api/';
        $srcDir = $this->getParameter('kernel.project_dir') . '/src/';
        
        if (str_starts_with($path, 'files/src/')) {
            // Handle requests for source code files
            $filePath = realpath($srcDir . substr($path, 10, -4)); // Remove 'files/src/' prefix and '.txt' suffix
            $allowedExtensions = ['php', 'twig', 'yaml', 'yml', 'xml', 'json'];
            
            if (!$filePath || !str_starts_with($filePath, $srcDir) || !in_array(pathinfo($filePath, PATHINFO_EXTENSION), $allowedExtensions)) {
                throw new AccessDeniedHttpException('Access to this file is not allowed.');
            }
            
            if (!file_exists($filePath)) {
                throw $this->createNotFoundException('The source file does not exist');
            }
            
            $content = file_get_contents($filePath);
            return new Response($content, 200, ['Content-Type' => 'text/plain']);
        }
        
        $filePath = realpath($docsDir . $path);

        // Ensure the file is within the docs directory
        if (!$filePath || !str_starts_with($filePath, $docsDir)) {
            throw new AccessDeniedHttpException('Access to this file is not allowed.');
        }

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('The documentation file does not exist');
        }

        // If it's an HTML file, we need to modify its content
        if (pathinfo($filePath, PATHINFO_EXTENSION) === 'html') {
            $content = file_get_contents($filePath);
            $content = $this->updateLinks($content);
            return $this->render('documentation/wrapper.html.twig', [
                'content' => $content,
            ]);
        }

        return new BinaryFileResponse($filePath);
    }

    /**
     * Update links in the HTML content to include the /documentation prefix.
     *
     * @param string $content The HTML content
     * @return string The modified HTML content
     */
    private function updateLinks(string $content): string
    {
        $content = preg_replace('/(href|src)="(?!http|https|\/\/|#)([^"]*)"/', '$1="/documentation/$2"', $content);
        return $content;
    }

    /**
     * Serve static assets for documentation.
     *
     * @param string $type The type of asset (css or js)
     * @param string $file The file name
     * @return BinaryFileResponse
     */
    #[Route('/documentation/{type}/{file}', name: 'app_documentation_asset', requirements: ['type' => 'css|js'])]
    public function serveAsset(string $type, string $file): BinaryFileResponse
    {
        $assetPath = $this->getParameter('kernel.project_dir') . "/public/documentation/{$type}/{$file}";
        
        if (!file_exists($assetPath)) {
            throw $this->createNotFoundException('The asset file does not exist');
        }

        return new BinaryFileResponse($assetPath);
    }
}
