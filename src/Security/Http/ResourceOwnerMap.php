<?php

namespace Sergiors\Silex\Security\Http;

use Pimple\Container;
use HWI\Bundle\OAuthBundle\Security\Http\ResourceOwnerMapInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class ResourceOwnerMap implements ResourceOwnerMapInterface
{
    /**
     * @var HttpUtils
     */
    protected $httpUtils;

    /**
     * @var array
     */
    protected $resourceOwners;

    /**
     * @var array
     */
    protected $possibleResourceOwners;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param HttpUtils $httpUtils              HttpUtils
     * @param array     $possibleResourceOwners array with possible resource owners names
     * @param array     $resourceOwners         array with configured resource owners
     */
    public function __construct(Container $container, HttpUtils $httpUtils, array $possibleResourceOwners, $resourceOwners)
    {
        $this->httpUtils = $httpUtils;
        $this->container = $container;
        $this->possibleResourceOwners = $possibleResourceOwners;
        $this->resourceOwners = $resourceOwners;
    }

    /**
     * Check that resource owner with given name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasResourceOwnerByName($name)
    {
        return isset($this->resourceOwners[$name], $this->possibleResourceOwners[$name]);
    }

    /**
     * Gets the appropriate resource owner given the name.
     *
     * @param string $name
     *
     * @return null|ResourceOwnerInterface
     */
    public function getResourceOwnerByName($name)
    {
        if (!$this->hasResourceOwnerByName($name)) {
            return null;
        }

        return $this->container['hwi_oauth.resource_owner.'.$name];
    }

    /**
     * Gets the appropriate resource owner for a request.
     *
     * @param Request $request
     *
     * @return null|array
     */
    public function getResourceOwnerByRequest(Request $request)
    {
        foreach ($this->resourceOwners as $name => $checkPath) {
            if ($this->httpUtils->checkRequestPath($request, $checkPath)) {
                return [$this->getResourceOwnerByName($name), $checkPath];
            }
        }

        return null;
    }

    /**
     * Gets the check path for given resource name.
     *
     * @param string $name
     *
     * @return null|string
     */
    public function getResourceOwnerCheckPath($name)
    {
        if (isset($this->resourceOwners[$name])) {
            return $this->resourceOwners[$name];
        }

        return null;
    }

    /**
     * Get all the resource owners.
     *
     * @return array
     */
    public function getResourceOwners()
    {
        return $this->resourceOwners;
    }
}