<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use Spatie\PdfToText\Pdf;
use Throwable;

class DocumentTextExtractor
{
    public function extract(UploadedFile $file): string
    {
        return match (strtolower($file->getClientOriginalExtension())) {
            'pdf' => $this->extractPdf($file->getRealPath()),
            'docx' => $this->extractDocx($file->getRealPath()),
            default => (string) file_get_contents($file->getRealPath()),
        };
    }

    private function extractPdf(string $path): string
    {
        try {
            return Pdf::getText($path);
        } catch (Throwable) {
            return '';
        }
    }

    private function extractDocx(string $path): string
    {
        try {
            $document = IOFactory::load($path);
            $lines = [];

            foreach ($document->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof Text) {
                        $lines[] = $element->getText();
                    }

                    if ($element instanceof TextRun) {
                        foreach ($element->getElements() as $child) {
                            if ($child instanceof Text) {
                                $lines[] = $child->getText();
                            }
                        }
                    }
                }
            }

            return implode(PHP_EOL, array_filter($lines));
        } catch (Throwable) {
            return '';
        }
    }
}

