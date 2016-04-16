<?php
/**
 * East Framework.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\Framework\Http\Client;

use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Teknoo\East\Framework\Http\ClientInterface;
use Teknoo\States\Proxy\IntegratedInterface;
use Teknoo\States\Proxy\IntegratedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;

/**
 * Class Client implementing ClientInterface to represent the client in the server side. The client is agnostic and 
 * accepts only \Throwable exception and PSR7 response.
 * All public method of the client must only return the self client or a clone instance.
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Client implements
    ProxyInterface,
    IntegratedInterface,
    ClientInterface
{
    use ProxyTrait,
        IntegratedTrait;

    /**
     * Class name of the factory to use in set up to initialize this object in this construction.
     *
     * @var string
     */
    protected static $startupFactoryClassName = '\Teknoo\States\Factory\StandardStartupFactory';
    
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
     * @param GetResponseEvent $event
     * @param HttpFoundationFactory $factory
     */
    public function __construct(GetResponseEvent $event, HttpFoundationFactory $factory)
    {
        $this->getResponseEvent = $event;
        $this->httpFoundationFactory = $factory;
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        //Call the startup factory to initialize this proxy
        $this->initializeObjectWithFactory();
        //Enable default pending
        $this->enableState('Pending');
    }

    /**
     * {@inheritdoc}
     */
    public function successfulResponseFromController(ResponseInterface $response): ClientInterface
    {
        return $this->doSuccessfulResponseFromController($response);
    }

    /**
     * {@inheritdoc}
     */
    public function errorInRequest(\Throwable $throwable): ClientInterface
    {
        return $this->doErrorInRequest($throwable);
    }
}
