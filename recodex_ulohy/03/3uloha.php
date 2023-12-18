<?php

/**
 * Represents the template compiler.
 */
class Templator
{
    private $templateContent;
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

        $this->templateContent = $content;
    }
     /**
     * Compile the loaded template (transpill it into interleaved-PHP) and save the result in a file.
     * @param string $fileName Path where the result should be saved.
     */
    public function compileAndSave(string $fileName)
    {
        if ($this->templateContent === null) {
            throw new Exception("Template not loaded. Call loadTemplate() first.");
        }

        $compiledContent = $this->compile($this->templateContent);

        if (is_dir($fileName)) {
            throw new Exception("Invalid file path. $fileName is a directory, not a file.");
        }
        
        $result = file_put_contents($fileName, $compiledContent);
        if ($result === false) {
            throw new Exception("Unable to save compiled template to $fileName");
        }
    } 

    private function isValidExpressionOrCondition($expr) {
        return !empty($expr) && strpos($expr, '{') === false && strpos($expr, '}') === false;
    }

    private function compile($input)
    {
        $output = '';
        $length = strlen($input);
        $i = 0;
        $markerStack = []; // Stack to keep track of markers

        while ($i < $length) {
            if ($input[$i] == '{' && $i + 2 < $length) {
                if ($input[$i + 1] == '=') {
                    // {= expr} - Expression marker
                    $i += 2;
                    $expr = '';
                    while ($i < $length && $input[$i] != '}') {
                        $expr .= $input[$i];
                        $i++;
                    }

                    if ($i >= $length) {
                        throw new Exception('Error: Missing closing curly brace for expression.');
                    }

                    if (!$this->isValidExpressionOrCondition($expr)) {
                        throw new Exception('Error: Invalid expression within curly braces.');
                    }

                    $output .= '<?= htmlspecialchars(' . $expr . ') ?>';
                    $i++;
                } elseif ($input[$i + 1] == 'i' && $input[$i + 2] == 'f') {
                    // {if cond} - If marker
                    $i += 3;
                    $cond = '';
                    while ($i < $length && $input[$i] != '}') {
                        $cond .= $input[$i];
                        $i++;
                    }

                    if ($i >= $length) {
                        throw new Exception('Error: Missing closing curly brace for condition.');
                    }

                    if (!$this->isValidExpressionOrCondition($cond)) {
                        throw new Exception('Error: Invalid condition within curly braces.');
                    }

                    $output .= '<?php if (' . $cond . ') { ?>';
                    $markerStack[] = 'if';
                    $i++;
                } elseif ($input[$i + 1] == 'f' && $input[$i + 2] == 'o' && $input[$i + 3] == 'r' && $input[$i + 4] !== 'e') {
                    // {for expr} - For marker
                    $i += 4;
                    $expr = '';
                    while ($i < $length && $input[$i] != '}') {
                        $expr .= $input[$i];
                        $i++;
                    }

                    if ($i >= $length) {
                        throw new Exception('Error: Missing closing curly brace for expression.');
                    }

                    if (!$this->isValidExpressionOrCondition($expr)) {
                        throw new Exception('Error: Invalid expression within curly braces.');
                    }

                    $output .= '<?php for (' . $expr . ') { ?>';
                    $markerStack[] = 'for';
                    $i++;
                } elseif ($input[$i + 1] == 'f' && $input[$i + 2] == 'o' && $input[$i + 3] == 'r' && $input[$i + 4] == 'e' && $input[$i + 5] == 'a' && $input[$i + 6] == 'c' && $input[$i + 7] == 'h') {
                    // {foreach expr} - Foreach marker
                    $i += 8;
                    $expr = '';
                    while ($i < $length && $input[$i] != '}') {
                        $expr .= $input[$i];
                        $i++;
                    }

                    if ($i >= $length) {
                        throw new Exception('Error: Missing closing curly brace for expression.');
                    }

                    if (!$this->isValidExpressionOrCondition($expr)) {
                        throw new Exception('Error: Invalid expression within curly braces.');
                    }

                    $output .= '<?php foreach (' . $expr . ') { ?>';
                    $markerStack[] = 'foreach';
                    $i++;
                } elseif ($input[$i + 1] == '/') {
                    // Marker closing tag
                    $i += 2;
                    $closingMarker = '';
                    while ($i < $length && $input[$i] != '}') {
                        $closingMarker .= $input[$i];
                        $i++;
                    }

                    if ($i >= $length) {
                        throw new Exception('Error: Missing closing curly brace for marker.');
                    }

                    if (empty($closingMarker)) {
                        throw new Exception('Error: Empty closing marker.');
                    }

                    if ($closingMarker !== 'for' && $closingMarker !== 'foreach' && $closingMarker !== 'if') {
                        $output .= '{/' . $closingMarker . '}';
                        $i++;
                        continue;
                    }
                    
                    $topMarker = array_pop($markerStack);
                    if ($topMarker !== $closingMarker) {
                        throw new Exception('Error: Mismatched marker closing tag.'. $topMarker .' '. $closingMarker);
                    }

                    $output .= '<?php } ?>';
                    $i++;
                } else {
                    // Not a recognized marker, treat as regular text
                    $output .= '{';
                    $i++;
                }
            } else {
                // Regular text
                $output .= $input[$i];
                $i++;
            }
        }

        if (!empty($markerStack)) {
            throw new Exception('Error: Unclosed marker(s) found.');
        }

        return $output;
    }
}