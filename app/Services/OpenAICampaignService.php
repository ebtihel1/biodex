namespace App\Services;

use OpenAI\Client;

class OpenAIService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'api_key' => env('OPENAI_API_KEY'),
        ]);
    }

    public function ask($prompt)
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4', // ou 'gpt-3.5-turbo'
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);

        return $response->choices[0]->message->content;
    }
}
