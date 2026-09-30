namespace App\Http\Controllers;

use App\Services\OpenAIService;

class AIController extends Controller
{
    protected OpenAIService $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
    }

    public function askAI()
    {
        $prompt = "Bonjour, écris-moi un haïku sur l'automne.";
        $answer = $this->openAI->ask($prompt);

        return response()->json(['answer' => $answer]);
    }
}
