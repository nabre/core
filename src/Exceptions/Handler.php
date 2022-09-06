<?php

namespace Nabre\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if((new \Nabre\Routing\RouteHierarchy)->routeRedirect($redirect)){
            return redirect($redirect);
        }
        return parent::{__FUNCTION__}($request,$e);
    }

    protected function getHttpExceptionView(HttpExceptionInterface $e)
    {
        $view = 'Nabre::errors/'.$e->getStatusCode();

        if (view()->exists($view)) {
            return $view;
        }

        $view = substr($view, 0, -2).'xx';

        if (view()->exists($view)) {
            return $view;
        }

        return null;
    }
}
