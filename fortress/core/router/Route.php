<?php

namespace fortress\core\router;

class Route {

  private $route;

  private $regex;

  private $controller_name;

  private $action_name;

  private $method;

  private $middleware_name;

  public function __construct($route, $regex, $controller_name, $action_name, $method = "GET", $middleware_name = null) {
    $this -> route = $route;
    $this -> regex = $regex;
    $this -> controller_name = $controller_name;
    $this -> action_name= $action_name;
    $this -> method = strtoupper($method);
    $this -> middleware_name = $middleware_name;
  }

  public function match($url) {
    return preg_match($this -> regex, $url);
  }

  public function action($request) {

    if ($this -> method == $request -> method()) {

      $url = $request -> url();
      $url_exploded = explode("/", $url);
      $route_exploded = explode("/", $this -> route);
      $variables = [];

      for ($i = 0; $i < count($url_exploded); $i++) {
        if (preg_match("#^\{\w+\:\w+\}$#", $route_exploded[$i])) {
          $var_info = explode(":", rtrim(trim($route_exploded[$i], "{"), "}"));
          $variables[] = $url_exploded[$i];
        }
      }

      array_unshift($variables, $request);

      $controller_folder = preg_match("#admin#", $url) ? "admin" : "controller";
      $controller = "eagle\\" . $controller_folder . "\\" . $this -> controller_name;
      $controller_obj = new $controller();

      $action_exists = method_exists($controller_obj, $this -> action_name);

      if ($this -> middleware_name) {

        $middleware = "eagle\\core\\middleware\\" . $this -> middleware_name;
        $middleware_obj = new $middleware();

        $r = $request;
        $is = $action_exists;
        $act = $this -> action_name;
        $contr = $controller_obj;
        $var = $variables;

        return $middleware_obj -> handle($request, function() use ($r, $is, $act, $contr, $var) {
          if ($is) {
            call_user_func_array([$contr, $act], $var);
          } else {
            return new Response404($r);
          }
        });
      } else {
        if ($action_exists) {
          call_user_func_array([$controller_obj, $this -> action_name], $variables);
        } else {
          return new Response404($request);
        }
      }

    } else {
      return new Response("Incorrent request method!", 400);
    }
  }

}
