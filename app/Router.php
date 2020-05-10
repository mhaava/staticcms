<?php
    namespace App;

    class Router
    {

        public static function index()
        {
            $request = $_SERVER['REQUEST_URI'];
            $request = str_replace('/index.php', '', $request);
            $allowedAjaxRequests = array(
                '/admin/ajax/saveImage' => 'saveImage',
                '/admin/ajax/saveText' => 'saveText',
            );
            require_once BASEPATH . '/app/AdminController.php';
            $admin = new \App\AdminController();
            if (!($request == '/admin' || preg_match( '/admin\/\.*/', $request ))) {
                return false;
            }

            if (isset($allowedAjaxRequests[$request])) {
                $method = $allowedAjaxRequests[$request];
                $admin->$method();
                return true;
            }

            $request = strval(str_replace('/admin', '', $request));
            $request = ($request == '/' || $request == '') ? 'index' : $request;
            $request = (file_exists(BASEPATH . '/output/' . $request . '/')) ? $request.'/index' : $request;
            if (!file_exists(BASEPATH . '/output/' . $request . '.html')) {
                http_response_code(404);
                die();
            } else {
                require BASEPATH . '/output/' . $request . '.html';
            }
            $admin->index();
            return true;
        }
    }