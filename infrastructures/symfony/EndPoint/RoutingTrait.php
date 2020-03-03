<?php

/*
 * East Foundation.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\East\FoundationBundle\EndPoint;

use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Teknoo\East\Foundation\EndPoint\EndPointInterface;
use Teknoo\East\Foundation\Http\ClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait to help developer to write endpoint with Symfony (also called controller) and reuse Symfony component like
 * router or twig engine?
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
trait RoutingTrait
{
    use ResponseFactoryTrait;

    protected ?RouterInterface $router = null;

    /**
     * To inject the router into the trait, needed to generate url.
     *
     * @param RouterInterface $router
     *
     * @return EastEndPointTrait
     */
    public function setRouter(RouterInterface $router): self
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param mixed  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    protected function generateUrl(
        string $route,
        $parameters = array(),
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        if (!$this->router instanceof RouterInterface) {
            throw new \LogicException('The router is not registered in your application.');
        }

        return $this->router->generate($route, $parameters, $referenceType);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param ClientInterface $client
     * @param string          $url    The URL to redirect to
     * @param int             $status The status code to use for the Response
     * @param array<string, mixed> $headers An array of values to inject into HTTP header response
     *
     * @return EndPointInterface
     */
    public function redirect(
        ClientInterface $client,
        string $url,
        int $status = 302,
        array $headers = []
    ): EndPointInterface {
        $response = $this->responseFactory->createResponse($status);

        $headers['location'] = $url;
        $response = $this->addHeadersIntoResponse($response, $headers);

        $client->acceptResponse($response);

        return $this;
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param ClientInterface $client
     * @param string          $route      The name of the route
     * @param array           $parameters An array of parameters
     * @param int             $status     The status code to use for the Response
     *
     * @return EndPointInterface
     */
    protected function redirectToRoute(
        ClientInterface $client,
        string $route,
        array $parameters = array(),
        int $status = 302
    ): self {
        return $this->redirect($client, $this->generateUrl($route, $parameters), $status);
    }
}
