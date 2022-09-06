<?php

namespace Nabre\Routing;

use Illuminate\Routing\ResourceRegistrar as OriginalRegistrar;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Str;
##App\Providers\AppServiceProvider::boot()
/*
$registrar = new \App\Classes\Routing\ResourceRegistrar($this->app['router']);
$this->app->bind('Illuminate\Routing\ResourceRegistrar', function () use ($registrar) {
    return $registrar;
});
*/

class ResourceRegistrar extends OriginalRegistrar
{
    // add data to the array
    /**
     * The default actions for a resourceful controller.
     *
     * @var array
     */
    protected $resourceDefaults = ['index', 'create', 'store', 'copy', 'show', 'edit', 'update', 'destroy','reset','position' /*,'pdf'*/];

    function getResourceDefault(){
        return $this->resourceDefaults;
    }

    protected function getResourceMethods($defaults, $options)
    {
        $methods = parent::{__FUNCTION__}( $defaults, $options );

        if(in_array('edit',$methods)){
            $methods[]='update';
        }

        if(in_array('create',$methods)){
            $methods[]='store';
        }


        if (isset($options['except'])) {
            $methods = array_diff(array_unique($methods), (array) $options['except']);
        }

        return array_values(array_intersect($defaults,$methods));
    }

    public function register($name, $controller, array $options = [])
    {
        if (isset($options['parameters']) && ! isset($this->parameters)) {
            $this->parameters = $options['parameters'];
        }

        // If the resource name contains a slash, we will assume the developer wishes to
        // register these resource routes with a prefix so we will set that up out of
        // the box so they don't have to mess with it. Otherwise, we will continue.
        if (Str::contains($name, '/')) {
            $this->prefixedResource($name, $controller, $options);

            return;
        }

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $base = $this->getResourceWildcard( $options['key'] ?? (last(explode('.', $name))) );

        $defaults = $this-> resourceDefaults;

        $collection = new RouteCollection;

        foreach ($this->getResourceMethods($defaults, $options) as $m) {
            $route = $this->{'addResource'.ucfirst($m)}(
                $name, $base, $controller, $options
            );

            if (isset($options['bindingFields'])) {
                $this->setResourceBindingFields($route, $options['bindingFields']);
            }

            $collection->add($route);
        }

        return $collection;
    }
    /**
     * Add the data method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @param  array   $options
     * @return \Illuminate\Routing\Route
     */
    protected function addResourceCopy($name, $base, $controller, $options)
    {
        $name = $this->getShallowName($name, $options);
        $uri = $this->getResourceUri($name).'/{'.$base.'}/copy';
        $action = $this->getResourceAction($name, $controller, 'copy', $options);
        return $this->router->match(array('COPY', 'PATCH'),$uri, $action);
    }

    protected function addResourceReset($name, $base, $controller, $options)
    {
        $name = $this->getShallowName($name, $options);
        $uri = $this->getResourceUri($name).'/{'.$base.'}/reset';
        $action = $this->getResourceAction($name, $controller, 'reset', $options);
        return $this->router->match(array('PUT', 'PATCH'),$uri, $action);
    }

    protected function addResourcePosition($name, $base, $controller, $options)
    {
        $name = $this->getShallowName($name, $options);
        $uri = $this->getResourceUri($name).'/position';
        $action = $this->getResourceAction($name, $controller, 'position', $options);
        return $this->router->match(['POST'],$uri, $action);
    }
}
