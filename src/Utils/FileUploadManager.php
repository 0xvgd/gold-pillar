<?php

namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class FileUploadManager
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiUrl;

    public function __construct(string $apiKey, string $apiUrl)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    private function base64ToImage($base64_string, $output_file)
    {
        $file = fopen($output_file, 'wb');
        $data = explode(',', $base64_string);

        fwrite($file, base64_decode($data[1]));
        fclose($file);

        return $output_file;
    }

    public function sendFromBase64($base64, $fileName)
    {
        $file = $this->base64ToImage($base64, $fileName);

        $client = new Client();
        $result = $client->request('POST', $this->apiUrl, [
            'headers' => [
                'x-client' => $this->apiKey,
            ],
            'multipart' => [
                [
                    'name' => 'file_name',
                    'filename' => $fileName,
                    'contents' => fopen($file, 'r'),
                ],
            ],
        ]);

        $response = [
            'status' => $result->getStatusCode(),
            'response' => json_decode($result->getBody(), true),
        ];

        return $response;
    }

    public function send(string $path, string $fileName): array
    {
        $client = new Client();
        $result = $client->request('POST', $this->apiUrl, [
            'headers' => [
                'x-client' => $this->apiKey,
            ],
            'multipart' => [
                [
                    'name' => 'file_name',
                    'filename' => $fileName,
                    'contents' => fopen($path, 'r'),
                ],
            ],
        ]);

        $response = [
            'status' => $result->getStatusCode(),
            'response' => json_decode($result->getBody(), true),
        ];

        return $response;
    }

    public function delete(string $url)
    {
        $client = new Client();
        try {
            $result = $client->request('delete', $url, [
                'headers' => [
                    'x-client' => $this->apiKey,
                ],
            ]);

            $response = [
                'status' => $result->getStatusCode(),
                'response' => json_decode($result->getBody(), true),
            ];
        } catch (\GuzzleHttp\Exception\RequestException $ex) {
            $response = json_decode($ex->getResponse()->getBody(), true);
        }

        return $response;
    }

    /**
     * @param string $url
     * @param string $user
     * @param string $pass
     *
     * @return array
     */
    public function exists($url)
    {
        $client = new Client();
        $unidade = 'KB';
        try {
            $result = $client->head($url);
            $tamanhoArquivo = $result->getHeader('Content-Length')[0];
            $tamanhoArquivo /= 1024;

            if ($tamanhoArquivo > 1000) {
                $tamanhoArquivo /= 1024;
                $unidade = 'MB';
            }

            return [
                'existe' => true,
                'tamanhoArquivo' => number_format($tamanhoArquivo, 2, ',', '').' '.$unidade,
            ];
        } catch (ClientException $e) {
            return [
                'existe' => false,
            ];
        }
    }
}
