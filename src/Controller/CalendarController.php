<?php

namespace App\Controller;

use App\Entity\Order;
use App\Model\CalendarOrder;
use App\Repository\OrderRepository;
use App\Repository\TimeDataRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/calendar', name: 'app_calendar_')]
class CalendarController extends AbstractController
{
    const DEFAULT_DURATION = 25;
    const DEFAULT_CURRENCY = 'UAH';

    public function __construct(
        private readonly TimeDataRepository $timeDataRepository,
        private readonly OrderRepository $orderRepository,
        private readonly MailerInterface $mailer,
        private readonly string $adminEmail
    ) {
    }

    #[Route('', name: 'default')]
    public function default(): Response
    {
        $order = ['startAt' => 'DESC'];
        $todayItems = $this->timeDataRepository->findBy([], $order);
        $tomorrowItems = $this->timeDataRepository->findBy([], $order);

        return $this->render('calendar/default.html.twig', [
            'todayItems' => $todayItems,
            'tomorrowItems' => $tomorrowItems,
        ]);
    }

    #[Route('/order', name: 'default_order')]
    public function defaultOrder(Request $request): Response
    {
        $order = ['startAt' => 'DESC'];
        $item = $this->timeDataRepository->findOneBy([], $order);

        return $this->order($item->getUuid(), $request);
    }

    #[Route('/{uuid}/order', name: 'order')]
    public function order(string $uuid, Request $request): Response
    {
        $item = $this->timeDataRepository->findOneBy(['uuid' => $uuid]);

        if(!$item) {
            throw $this->createNotFoundException(sprintf('Item %s not found', $uuid)); //TODO: translate
        }

        $orderRequest = new CalendarOrder();
        $form = $this->createFormBuilder($orderRequest)
            ->add('date', HiddenType::class, [
                'data' => $uuid,
            ])
            ->add('name')
            ->add('email')
            ->add('phone')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $orderRequest->date = $item->getTime();

            //TODO: create from Order factory

            $order = new Order();
            $order->setAmount($item->getDuration() * self::DEFAULT_DURATION);
            //TODO: issues/211
            $order->setCurrency(self::DEFAULT_CURRENCY);
            $order->setDeliveryEmail($orderRequest->email);
            $order->setDeliveryPhone($orderRequest->phone);
            $order->setDeliveryName($orderRequest->name);
            $order->setDescription($orderRequest->date);

            $this->orderRepository->add($order, true);

            //TODO: issues/210
            $email = (new TemplatedEmail())
                ->from(new Address('info@triangle.software', 'Triangle Software'))
                ->to($this->adminEmail)
                ->subject(sprintf('New order for %s', $orderRequest->date))
                ->htmlTemplate('email/new_order.html.twig')
                ->context([
                    'order' => $orderRequest,
                ])
            ;
            $this->mailer->send($email);

            $this->addFlash('success', sprintf('Your order %s has been sent', $order->getUuid())); //TODO: translate

            return $this->redirectToRoute('app_calendar_order_success');
        }

        return $this->render('calendar/calendar_order.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/success', name: 'order_success')]
    public function orderSuccess(): Response
    {
        return $this->render('calendar/calendar_order_success.html.twig');
    }
}
