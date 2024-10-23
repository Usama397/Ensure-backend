<?php

namespace App\Http\Middleware;

use App\Models\AppLog;
use Closure;
use Illuminate\Http\Request;

class LogRoute
{
    protected $appLogID = '';
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $action = $request->route()->getAction();

        $controller = class_basename($action['controller']);

        list($controller, $action) = explode('@', $controller);
        $endPoint = explode('/', $request->getUri());

        $logger = new AppLog();

        $logger->uri = $request->getUri();
        $logger->endpoint = 'api/'.end($endPoint);
        $logger->method = $request->getMethod();
        $logger->ip = $request->ip();
        $logger->request_body = json_encode($request->all());
        $logger->response = json_encode($request->getContent());
        $logger->action = $action;
        $logger->controller = $controller;
        if($request->user()){
            $logger->user_id = $request->user()->id;
        }
        $logger->save();
        $this->appLogID = $logger->id;
        return $response;
    }

    public function terminate($request, $response)
    {
        if($this->appLogID != '' && $response)
        {
            $appLog = AppLog::find($this->appLogID);
            $appLog->response = $response->getContent();
            $appLog->save();
        }
        return $response;
    }
}
