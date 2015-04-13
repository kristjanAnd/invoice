<?php

namespace Application\View\Helper;

use Zend\Mvc\Router\RouteMatch;
use Zend\View\Helper\AbstractHelper;

class CurrentRoute extends AbstractHelper
{
    /**
     * @var RouteMatch
     */
    private $routeMatch;
    private $topLevelRoute;
    private $routeDelimiter = '/';

    public function setRouteMatch(RouteMatch $routeMatch = null)
    {
        $this->routeMatch = $routeMatch;

        return $this;
    }

    public function setTopLevelRoute($topLevelRoute)
    {
        $this->topLevelRoute = $topLevelRoute;

        return $this;
    }

    public function __invoke()
    {
        if ($this->routeMatch == null) {
            return $this->addTopLevelRoute('home');
        }

        $routeName = $this->routeMatch->getMatchedRouteName();
        if (strpos($routeName, $this->topLevelRoute) === false) {
            $routeName = $this->addTopLevelRoute($routeName);
        }

        return $routeName;
    }

    private function addTopLevelRoute($route)
    {
        if ($this->topLevelRoute === null) {
            return $route;
        }
        return $this->topLevelRoute . $this->routeDelimiter . $route;
    }
}
