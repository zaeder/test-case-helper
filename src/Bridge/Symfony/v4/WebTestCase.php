<?php

namespace Zaeder\PhpUnit\Bridge\Symfony\v4;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

abstract class WebTestCase extends KernelTestCase
{
    use \Symfony\Bundle\FrameworkBundle\Test\ForwardCompatTestTrait;
    use \Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
    use \Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

    private function doTearDown()
    {
        parent::tearDown();
        self::getClient(null);
    }

    /**
     * Creates a KernelBrowser.
     *
     * @param array $options An array of options to pass to the createKernel method
     * @param array $server  An array of server parameters
     *
     * @return KernelBrowser A KernelBrowser instance
     */
    protected static function createClient(array $options = [], array $server = [])
    {
        if (static::$booted) {
            @trigger_error(sprintf('Calling "%s()" while a kernel has been booted is deprecated since Symfony 4.4 and will throw an exception in 5.0, ensure the kernel is shut down before calling the method.', __METHOD__), E_USER_DEPRECATED);
        }

        $kernel = static::bootKernel($options);

        try {
            $client = $kernel->getContainer()->get('test.client');
        } catch (ServiceNotFoundException $e) {
            if (class_exists(KernelBrowser::class)) {
                throw new \LogicException('You cannot create the client used in functional tests if the "framework.test" config is not set to true.');
            }
            throw new \LogicException('You cannot create the client used in functional tests if the BrowserKit component is not available. Try running "composer require symfony/browser-kit".');
        }

        $client->setServerParameters($server);

        return self::getClient($client);
    }
}