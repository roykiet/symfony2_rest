<?php
namespace Acme\BlogBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MailHandler
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var object
     */
    private $templating;

    public function __construct(ContainerInterface $container)
    {
        $this->mailer = $container->get('mailer');
        $this->templating = $container->get('templating');
    }

    public function send()
    {
        $message = \Swift_Message::newInstance()
                                 ->setSubject('Created Post')
                                 ->setFrom('omsqaautomation@gmail.com')
                                 ->setTo('omsqaautomation@gmail.com')
                                 ->setBody(
                                     $this->templating->render(
                                         'Emails/notification.html.twig'
                                     ),
                                     'text/html'
                                 );
        $this->mailer->send($message);
    }
}