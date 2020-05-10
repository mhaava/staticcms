<?php
    namespace App;

    use DOMDocument;
    use DOMXPath;

    class Dom
    {
        private static function getNodesByClass($class, $dom, $index)
        {
            $finder = new DomXPath($dom);
            //https://stackoverflow.com/questions/6366351/getting-dom-elements-by-classname
            $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]");
            if(!isset($nodes[$index])){
                return false;
            }
            return $nodes;
        }

        public static function replaceText($file, $index, $content, $class)
        {
            $dom = new DOMDocument();
            $dom->loadHTMLFile($file);
            $nodes = self::getNodesByClass($class, $dom, $index);

            while($nodes[$index]->childNodes->length){
                $nodes[$index]->removeChild($nodes[$index]->firstChild);
            }
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML($content);
            $nodes[$index]->appendChild($fragment);
            file_put_contents($file, $dom->saveHTML($dom));
            return true;
        }

        public static function replaceImage($file, $index, $src, $class)
        {
            $dom = new DOMDocument();
            $dom->loadHTMLFile($file);
            $nodes = self::getNodesByClass($class, $dom, $index);

            $length = $nodes[$index]->attributes->length;
            for($i=0; $i<$length; $i++) {
                $item = $nodes[$index]->attributes->item($i);
                if ($item->name == 'src') {
                    $item->value = $src;
                    break;
                }
            }
            file_put_contents($file, $dom->saveHTML($dom));
            return true;
        }
    }