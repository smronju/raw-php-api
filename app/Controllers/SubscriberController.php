<?php

namespace App\Controllers;

use App\Services\SubscriberService;
use Rakit\Validation\Validator;

class SubscriberController
{
    private SubscriberService $subscriber;

    public function __construct(public $requestMethod, public $databaseConnection, public $subscriberId)
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
            'DELETE' => $this->deleteSubscriber(),
            default => $this->notFoundResponse(),
        };

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getSubscriber(): array
    {
        if ($this->subscriberId) {
            $result = $this->subscriber->find($this->subscriberId);

            if (empty($result)) {
                return $this->notFoundResponse();
            }
        } else {
            $result = $this->subscriber->paginate(5);
        };

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    private function deleteSubscriber() {
        if ($this->subscriberId && $this->subscriber->find($this->subscriberId)) {
            $this->subscriber->delete($this->subscriberId);
            $response['status_code_header'] = 'HTTP/1.1 200 Okay';
            $response['body'] = json_encode(['Subscriber deleted.']);

            return $response;
        }

        return $this->notFoundResponse();
    }

    private function createSubscriberFromRequest(): array
    {
        if (isset($_REQUEST['email']) && $this->subscriber->find($_REQUEST['email'])) {
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