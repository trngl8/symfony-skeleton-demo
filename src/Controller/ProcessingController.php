<?php

namespace App\Controller;

use App\Model\ExternalOrder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProcessingController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Route(path: "/api/order/create", name: "api_order_create", methods: ["POST"])]
    public function createOrder(Request $request) : Response
    {
        $data = $request->request->all() ?? [];

        $order = new ExternalOrder($data);

        $errors = $this->validator->validate($order);

        if (count($errors) > 0) {
            $result = [];
            foreach ($errors as $error) {
                $result = array_merge($result, [[
                    'property' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ]]);
            }
            return $this->json([
                'status' => 'error',
                'errors' => $result
            ], 400);
        }

        $this->logger->info('New order', ['order' => $order]);

        return $this->json([
            'status' => 'new'
        ]);
    }
}
