<?php
/**
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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\FoundationBundle\Http;

use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Teknoo\East\Foundation\Http\ClientInterface;

/**
 * Default implementation of Teknoo\East\Foundation\Http\ClientInterface and
 * Teknoo\East\FoundationBundle\Http\ClientWithResponseEventInterface to create client integrated with Symfony and able
 * to manage GetResponseEvent instance from Symfony Kernel loop.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Client implements ClientWithResponseEventInterface
{
    /**
     * @var GetResponseEvent
     */
    private $getResponseEvent;

    /**
     * @var HttpFoundationFactory
     */
    private $httpFoundationFactory;

    /**
     * Client constructor.
     *
     * @param HttpFoundationFactory $factory
     * @param GetResponseEvent|null $getResponseEvent
     */
    public function __construct(HttpFoundationFactory $factory, GetResponseEvent $getResponseEvent = null)
    {
        $this->httpFoundationFactory = $factory;
        if ($getResponseEvent instanceof GetResponseEvent) {
            $this->setGetResponseEvent($getResponseEvent);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setGetResponseEvent(GetResponseEvent $getResponseEvent): ClientWithResponseEventInterface
    {
        $this->getResponseEvent = $getResponseEvent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function responseFromController(ResponseInterface $response): ClientInterface
    {
        if (!$this->getResponseEvent instanceof GetResponseEvent) {
            throw new \RuntimeException('Error, the getResponseEvent has not been set into the client');
        }

        $this->getResponseEvent->setResponse(
            $this->httpFoundationFactory->createResponse($response)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function errorInRequest(\Throwable $throwable): ClientInterface
    {
        if (!$this->getResponseEvent instanceof GetResponseEvent) {
            throw new \RuntimeException('Error, the getResponseEvent has not been set into the client');
        }

        $this->getResponseEvent->setResponse(
            new Response(
                $throwable->getMessage(),
                500
            )
        );

        return $this;
    }

    /**
     * To deep clone all elements of the client
     */
    public function __clone()
    {
        $this->httpFoundationFactory = clone $this->httpFoundationFactory;
        if (!empty($this->getResponseEvent)) {
            $this->getResponseEvent = clone $this->getResponseEvent;
        }
    }
}
