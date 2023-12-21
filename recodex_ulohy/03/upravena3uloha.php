<?php

/**
 * Represents the template compiler.
 */
class Templator
{
    private $fileContent;
    /**
     * Load a template file into memory.
     * @param string $fileName Path to the template file to be loaded.
     */
    public function loadTemplate(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new Exception("Template file $fileName does not exist");
        }

        $content = file_get_contents($fileName);
        if ($content === false) {
            throw new Exception("Unable to load template from $fileName");
        }

        $this->fileContent = $content;
    }

    private function grabContent(&$expr, &$charIndex, &$length, &$fileContent) {
        while ($charIndex < $length && $fileContent[$charIndex] != '}') {
            $expr .= $fileContent[$charIndex];
            $charIndex++;
        }
    }

    private function isValid($expr) {
        return !empty($expr) && strpos($expr, '{') === false && strpos($expr, '}') === false;
    }

    private function checkExpressionValidity($charIndex, $length, $expr)
    {
        if ($charIndex >= $length) {
            throw new Exception('Error: Missing closing curly brace for expression.');
        }

        if (!$this->isValid($expr)) {
            throw new Exception('Error: Invalid expression within curly braces.');
        }
    }


    private function formCompiler($fileContent)
    {
        $compiledContent = '';
        $length = strlen($fileContent);
        $charIndex = 0;
        $stack = [];

        while ($charIndex < $length) {
            if ($fileContent[$charIndex] == '{' && $charIndex + 2 < $length) {
                if ($fileContent[$charIndex + 1] == '=') {
                    $charIndex += 2;
                    $expr = '';
                    $this->grabContent($expr, $charIndex, $length, $fileContent);
                    $this->checkExpressionValidity($charIndex, $length, $expr);
                    $compiledContent .= '<?= htmlspecialchars(' . $expr . ') ?>';
                    $charIndex++;
                } elseif ($fileContent[$charIndex + 1] == 'i' && $fileContent[$charIndex + 2] == 'f') {
                    $charIndex += 3;
                    $expr = '';
                    $this->grabContent($expr, $charIndex, $length, $fileContent); 
                    $this->checkExpressionValidity($charIndex, $length, $expr);

                    $compiledContent .= '<?php if (' . $expr . ') { ?>';
                    $stack[] = 'if';
                    $charIndex++;
                } elseif ($fileContent[$charIndex + 1] == 'f' && $fileContent[$charIndex + 2] == 'o' && $fileContent[$charIndex + 3] == 'r' && $fileContent[$charIndex + 4] !== 'e') {
                    $charIndex += 4;
                    $expr = '';
                    $this->grabContent($expr, $charIndex, $length, $fileContent);
                    $this->checkExpressionValidity($charIndex, $length, $expr);
                    $compiledContent .= '<?php for (' . $expr . ') { ?>';
                    $stack[] = 'for';
                    $charIndex++;
                } elseif ($fileContent[$charIndex + 1] == 'f' && $fileContent[$charIndex + 2] == 'o' && $fileContent[$charIndex + 3] == 'r' && $fileContent[$charIndex + 4] == 'e' && $fileContent[$charIndex + 5] == 'a' && $fileContent[$charIndex + 6] == 'c' && $fileContent[$charIndex + 7] == 'h') {
                    $charIndex += 8;
                    $expr = '';
                    $this->grabContent($expr, $charIndex, $length, $fileContent);
                    $this->checkExpressionValidity($charIndex, $length, $expr);
                    $compiledContent .= '<?php foreach (' . $expr . ') { ?>';
                    $stack[] = 'foreach';
                    $charIndex++;
                } elseif ($fileContent[$charIndex + 1] == '/') {
                    $charIndex += 2;
                    $closingMarker = '';
                    $this->grabContent($closingMarker, $charIndex, $length, $fileContent);

                    if ($charIndex >= $length) {
                        throw new Exception('Error: Missing closing curly brace for marker.');
                    }

                    if (empty($closingMarker)) {
                        throw new Exception('Error: Empty closing marker.');
                    }

                    if ($closingMarker !== 'for' && $closingMarker !== 'foreach' && $closingMarker !== 'if') {
                        $compiledContent .= '{/' . $closingMarker . '}';
                        $charIndex++;
                        continue;
                    }
                    
                    $topMarker = array_pop($stack);
                    if ($topMarker !== $closingMarker) {
                        throw new Exception('Error: Mismatched marker closing tag.'. $topMarker .' '. $closingMarker);
                    }

                    $compiledContent .= '<?php } ?>';
                    $charIndex++;
                } else {
                    $compiledContent .= '{';
                    $charIndex++;
                }
            } else {
                $compiledContent .= $fileContent[$charIndex];
                $charIndex++;
            }
        }

        if (!empty($stack)) {
            throw new Exception('Error: Unclosed marker(s) found.');
        }

        return $compiledContent;
    }




     /**
     * Compile the loaded template (transpill it into interleaved-PHP) and save the result in a file.
     * @param string $fileName Path where the result should be saved.
     */
    public function compileAndSave(string $fileName)
    {
        if ($this->fileContent === null) {
            throw new Exception("Nothing to compile. Please load a template first.");
        }

        $compiledContent = $this->formCompiler($this->fileContent);

        if (is_dir($fileName)) {
            throw new Exception("Unable to save compiled template to $fileName. It is a directory.");
        }
        
        $result = file_put_contents($fileName, $compiledContent);
        if ($result === false) {
            throw new Exception("Unable to save compiled template to $fileName");
        }
    } 

}