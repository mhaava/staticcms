<?php

    namespace App;

    use DOMDocument;
    use DOMXPath;
    use const BASEPATH;
    require_once BASEPATH. 'app/Dom.php';

    class AdminController
    {
        protected $config;

        public function __construct()
        {
            $this->loadConfig();
            $this->authenticate();
        }

        protected function loadConfig()
        {
            $filename = BASEPATH. "config.json";
            $configFile = fopen($filename, "r") or die("Unable to open file!");
            $this->config = json_decode(fread($configFile,filesize($filename)));
            fclose($configFile);
        }

        protected function authenticate()
        {
            $username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
            $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
            $validated = false;
            foreach ($this->config->administrators as $admin) {
                if ($admin->username == $username) {
                    $validated = ($admin->password == $password);
                    break;
                }
            }
            if (!$validated) {
                header('WWW-Authenticate: Basic realm="My Realm"');
                header('HTTP/1.0 401 Unauthorized');
                die();
            }
        }

        protected function cleanUrl($url)
        {
            $url = str_replace(array('/index.php', '/admin'), '', $url);
            $url = ($url == '/' || $url == '') ? 'index' : $url;
            return (file_exists(BASEPATH . '/output/' . $url . '/')) ? $url.'/index' : $url;
        }

        protected function replaceContentInDom($domMethod, $url, $index, $content, $class)
        {
            $file = BASEPATH . '/output/' . $url . '.html';
            if (!file_exists($file)) {
                return false;
            }
            Dom::$domMethod($file, $index, $content, $class);
            $url = explode('/', trim($url, '/'));
            $isDefaultLanguage = true;
            $defaultLanguageCode = false;
            foreach ($this->config->localization as $language) {
                if ($language->code == $url[0]) {
                    $isDefaultLanguage = false;
                }
                if ($language->default) {
                    $defaultLanguageCode = $language->code;
                    if ($language->code == $url[0]) {
                        array_shift($url);
                        $url = implode('/', $url);
                        Dom::$domMethod(BASEPATH . '/output/' . $url . '.html', $index, $content, $class);
                        break;
                    }
                }
            }
            if($isDefaultLanguage) {
                array_unshift($url, $defaultLanguageCode);
                $url = implode('/', $url);
                Dom::$domMethod(BASEPATH . '/output/' . $url . '.html', $index, $content, $class);
            }
            return true;
        }

        public function index()
        {
            require  BASEPATH.'/app/editor.php';
        }

        public function saveText()
        {
            $return['success'] = false;
            $index = $_POST['index'];
            $content = $_POST['content'];
            $url = $this->cleanUrl($_POST['url']);
            $return['success'] = $this->replaceContentInDom('replaceText', $url, $index, $content, "cms-editable-text");
            header('Content-type: application/json');
            echo json_encode($return);
        }

        public function saveImage()
        {
            $return['success'] = false;
            $index = $_POST['index'];
            $src = $_POST['src'];
            $url = $this->cleanUrl($_POST['url']);
            $return['success'] = $this->replaceContentInDom('replaceImage', $url, $index, $src, "cms-editable-image");
            header('Content-type: application/json');
            echo json_encode($return);
        }
    }