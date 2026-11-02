<?php

namespace App\Services;

class SimplePdfParser
{
    public function parseFile($filename)
    {
        $content = file_get_contents($filename);
        $text = $this->extractText($content);
        return $text;
    }

    private function extractText($content)
    {
        $text = '';
        
        // Handle text in streams (compressed or uncompressed)
        if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $stream_matches)) {
            foreach ($stream_matches[1] as $stream) {
                $decoded = $this->decodeStream($stream);
                $text .= $this->extractTextFromStream($decoded);
            }
        }
        
        // If minimal text found, try to extract from raw content (for uncompressed PDFs)
        if (strlen($text) < 10) {
            $text .= $this->extractTextFromStream($content);
        }

        return $text;
    }

    private function decodeStream($stream)
    {
        // Try to uncompress
        $decoded = @gzuncompress($stream);
        if ($decoded === false) {
            return $stream; // Might be uncompressed or using other filter
        }
        return $decoded;
    }

    private function extractTextFromStream($stream)
    {
        $text = '';
        
        // Extract text between parentheses (...)
        if (preg_match_all('/\((.*?)\)/s', $stream, $matches)) {
            foreach ($matches[1] as $match) {
                // Handle escaped characters
                $match = str_replace(['\(', '\)'], ['(', ')'], $match);
                $text .= $match . ' ';
            }
        }
        
        // Extract text from TJ arrays [...]TJ
        if (preg_match_all('/\[(.*?)\]\s*TJ/s', $stream, $matches)) {
            foreach ($matches[1] as $match) {
                if (preg_match_all('/\((.*?)\)/s', $match, $sub_matches)) {
                    foreach ($sub_matches[1] as $sub_match) {
                        $sub_match = str_replace(['\(', '\)'], ['(', ')'], $sub_match);
                        $text .= $sub_match . ' ';
                    }
                }
            }
        }

        return $text;
    }
}
