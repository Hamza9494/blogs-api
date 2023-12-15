<?php

class ProcessBlogsRequests
{
    public $author_id;

    public function __construct(private BlogGateway $gateway, $user_id)
    {
        $this->author_id = $user_id;
    }

    public function processRequests($method, $id)
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method, $this->author_id);
        }
    }

    private function processCollectionRequest($method, $writer_id)
    {
        switch ($method) {
            case 'GET':
                echo json_encode($this->gateway->getAll($writer_id), true);
                break;
            case 'POST':
                $blog = json_decode(file_get_contents("php://input"), true);
                $errors = $this->validateBlog($blog);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $id = $this->gateway->create($this->author_id, $blog);
                http_response_code(201);
                echo json_encode([
                    "message" => 'blog added successfuly ',
                    "id" => $id,

                ]);
                break;
            default:
                http_response_code(405);
                header("allow: GET , POST");
        }
    }

    private function processResourceRequest($method, $id)
    {
        $blog = $this->gateway->get($id);
        if (!$blog) {
            http_response_code(404);
            echo json_encode([
                "error" => 'blog could not be found my dude '
            ]);
            return;
        }

        switch ($method) {
            case 'GET':
                echo json_encode($blog);
                break;
            case 'PUT':
                $current_blog = $this->gateway->get($id);
                $new_blog = json_decode(file_get_contents("php://input"), true);
                $rows =  $this->gateway->update($new_blog, $current_blog);
                echo json_encode(["message" => "blog updated successfully", "rows" => $rows]);
                break;
            case 'DELETE':
                $row = $this->gateway->delete($id);
                if ($row) {
                    echo  json_encode([
                        "message" => "blog deleted",
                        "rows" => $row
                    ]);
                };
                break;
            default:
                http_response_code(405);
                header("Allow: GET , PUT , DELETE");
        }
    }

    private function validateBlog($blog, $is_new = true)
    {
        $errors = [];

        if (empty($blog['author'])) {
            $errors[] = "author field required";
        }
        if (empty($blog['title'])) {
            $errors[] = "title field required";
        }
        if (empty($blog['body'])) {
            $errors[] = "body field required";
        }

        return $errors;
    }
}
