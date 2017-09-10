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

namespace Teknoo\East\FoundationBundle\Listener;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Teknoo\East\Foundation\Manager\ManagerInterface;
use Teknoo\East\FoundationBundle\Http\ClientWithResponseEventInterface;

/**
 * Class KernelListener to listen the event "kernel.request" sent by Symfony and pass requests to the East Foundation's
 * manager to be processed. See http://symfony.com/doc/current/reference/events.html#kernel-request.
 *
 * It converts Symfony Request to PSR Request (East Foundation accepts use only PSR Request and Response).
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class KernelListener
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var ClientWithResponseEventInterface
     */
    private $clientWithResponseEvent;

    /**
     * @var DiactorosFactory
     */
    private $diactorosFactory;

    /**
     * KernelListener constructor.
     *
     * @param ManagerInterface                 $manager
     * @param ClientWithResponseEventInterface $event
     * @param DiactorosFactory                 $diactorosFactory
     */
    public function __construct(
        ManagerInterface $manager,
        ClientWithResponseEventInterface $event,
        DiactorosFactory $diactorosFactory
    ) {
        $this->manager = $manager;
        $this->clientWithResponseEvent = $event;
        $this->diactorosFactory = $diactorosFactory;
    }

    /**
     * To transform a symfony request as a psr request and inject the symfony request as attribute if the endpoint need
     * the symfony request.
     *
     * @param Request $symfonyRequest
     * @return ServerRequestInterface
     */
    private function getPsrRequest(Request $symfonyRequest): ServerRequestInterface
    {
        $psrRequest = $this->diactorosFactory->createRequest($symfonyRequest);
        $psrRequest = $psrRequest->withAttribute('request', $symfonyRequest);

        return $psrRequest;
    }


    /**
     * @param GetResponseEvent $event
     *
     * @return KernelListener
     */
    public function onKernelRequest(GetResponseEvent $event): KernelListener
    {
        //To ignore sub request generated by symfony to handle non catch exception
        $request = $event->getRequest();
        if (!empty($request->attributes->has('exception'))) {
            return $this;
        }

        $client = clone $this->clientWithResponseEvent;
        $client->setGetResponseEvent($event);

        $psrRequest = $this->getPsrRequest($event->getRequest());
        $this->manager->receiveRequestFromClient($client, $psrRequest);

        return $this;
    }
}
