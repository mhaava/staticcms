<?php
    namespace Resources;

    use function in_array;
    use function json_decode;
    use function mkdir;
    use function var_dump;

    class Generator
    {

        public function __construct()
        {
            define('BASEPATH_GENERATOR', realpath(__DIR__).DIRECTORY_SEPARATOR);
        }

        public function console()
        {
            $config = $this->printConfig();
            echo "Is this correct? (yes/no):\n";
            $choice = trim( fgets(STDIN) );
            switch ($choice) {
                case ('yes'):
                    $this->run($config);
                    echo "Done\n";
                break;
                case ('no'):
                    echo "Please correct your configuration in config.json!\n";
                break;
            }
        }

        public function printConfig()
        {
            $filename = "config.json";
            $configFile = fopen($filename, "r") or die("Unable to open file!");
            $config = json_decode(fread($configFile,filesize($filename)));
            fclose($configFile);
            echo "Localization:\n";
            foreach ($config->localization as $language) {
                echo $language->code. ' - ' . $language->name . ($language->default ? ' - Default' : '') . "\n";
            }
            return $config;
        }

        public function run($config)
        {
            $input = BASEPATH_GENERATOR.'input';
            $output = BASEPATH_GENERATOR.'output';
            $this->recurse_rmdir($output, array('admin', '.htaccess'));
            $files = array_diff(scandir($input), array('..', '.'));
            $assets = $input . DIRECTORY_SEPARATOR . 'assets';
            if (is_dir($assets)) {
                try {
                    $this->recurse_copy($assets, BASEPATH_GENERATOR. 'output/assets');
                } catch (\Exception $e) {
                    echo $e;
                }
                if (($key = array_search('assets', $files)) !== false) {
                    unset($files[$key]);
                }
            }
            foreach ($config->localization as $language) {
                $newDir = $output. DIRECTORY_SEPARATOR. $language->code;
                @mkdir($newDir);
                foreach ($files as $file) {
                    copy($input. '/'. $file, $newDir. DIRECTORY_SEPARATOR. $file);
                    if ($language->default) {
                        copy($input. '/'. $file, $output. DIRECTORY_SEPARATOR. $file);
                    }
                }
            }
        }

        //https://www.php.net/manual/en/function.copy.php#91010
        public function recurse_copy($src,$dst) {
            $dir = opendir($src);
            @mkdir($dst);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir($src . '/' . $file) ) {
                        $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                    }
                    else {
                        $this->copy($src . '/' . $file,$dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

        public function recurse_rmdir($dir, $exeptions = array()) {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if (in_array($object, $exeptions)) {
                        continue;
                    }
                    if ($object != "." && $object != "..") {
                        if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                            $this->recurse_rmdir($dir. DIRECTORY_SEPARATOR .$object);
                        else
                            unlink($dir. DIRECTORY_SEPARATOR .$object);
                    }
                }
                if ($dir != BASEPATH_GENERATOR.'output') {
                    rmdir($dir);
                }
            }
        }

        public function copy($from, $to)
        {
            if (!copy($from, $to)) {
                throw new \Exception('File Copy Failed!');
            }
            return true;
        }
    }
    $gen = new Generator();
    $gen->console();