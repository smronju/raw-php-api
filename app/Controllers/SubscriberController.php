<?php

namespace App\Controllers;

use App\Services\SubscriberService;
use Rakit\Validation\Validator;

class SubscriberController
{
    private SubscriberService $subscriber;

    public function __construct(public $requestMethod, public $databaseConnection)
    {
        $this->subscriber = new SubscriberService($databaseConnection);
    }

    public function seedDatabase()
    {
        $result = $this->subscriber->seedSubscriber();

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['Table created and seeded some data']);

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function processRequest()
    {
        $response = match ($this->requestMethod) {
            'GET' => $this->getSubscriber(),
            'POST' => $this->createSubscriberFromRequest(),
            default => $this->notFoundResponse(),
        };

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getSubscriber(): array
    {
        $subscribers = $this->subscriber->paginate(10);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($subscribers);

        return $response;
    }

    private function createSubscriberFromRequest(): array
    {
        if ($this->subscriber->find($_REQUEST['email'])) {
            $response['status_code_header'] = 'HTTP/1.1 302 Found';
            $response['body'] = json_encode(['Subscriber already exist.']);

            return $response;
        }

        $validator = new Validator;

        $validation = $validator->make(
            $_POST + $_FILES,
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'status' => 'numeric'
            ]
        );

        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
            $response['body'] = json_encode($errors->all());

            return $response;
        }

        $this->subscriber->insert($validation->getValidData());

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode($validation->getValidData());

        return $response;
    }

    private function notFoundResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(['Not found!']);

        return $response;
    }

}