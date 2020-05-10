<?php
    namespace App;

    use DOMDocument;
    use DOMXPath;

    class Dom
    {
        private static function getNodeByClass($class, $dom, $index)
        {
            $finder = new DomXPath($dom);
            //https://stackoverflow.com/questions/6366351/getting-dom-elements-by-classname
            $node = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]")[$index];
            if(!isset($node)){
                return false;
            }
            return $node;
        }

        public static function replaceText($file, $index, $content, $class)
        {
            $dom = new DOMDocument();
            $dom->loadHTMLFile($file);
            $node = self::getNodeByClass($class, $dom, $index);

            while($node->childNodes->length){
                $node->removeChild($node->firstChild);
            }
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML($content);
            $node->appendChild($fragment);
            file_put_contents($file, $dom->saveHTML($dom));
            return true;
        }

        public static function replaceImage($file, $index, $src, $class)
        {
            $dom = new DOMDocument();
            $dom->loadHTMLFile($file);
            $node = self::getNodeByClass($class, $dom, $index);
            foreach ($node->attributes as $attribute) {
                if ($attribute->name == 'src') {
                    $attribute->value = $src;
                    break;
                }
            }
            file_put_contents($file, $dom->saveHTML($dom));
            return true;
        }
    }