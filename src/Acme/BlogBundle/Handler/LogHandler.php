<?php
 /**
 * @package    symfony2_rest
 * @copyright  Copyright (c) 2013 LaZaDa SEA
 * @author     long.nguyen-thanh <long.nguyen-thanh@lazada.com
 * @date       6/25/15 11:39 AM
 */
namespace Acme\BlogBundle\Handler;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LogHandler
{
    /**
     * @var object
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get('logger');
    }

    public function error($content)
    {
        $this->logger->error($content);
    }

    public function info($content)
    {
        $this->logger->info($content);
    }
}