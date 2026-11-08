<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Konsultant Kliniki: odpowiada modelem z Ollama, z historią rozmowy, danymi kolekcji użytkownika i pasującymi poradami
 * z `ClinicKnowledge` jako podstawą. Gdy model jest niedostępny, odpowiada gotową poradą z bazy wiedzy.
 */
final class ClinicConsultant
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
        Jesteś doświadczonym opiekunem bonsai i doradzasz użytkownikowi aplikacji KOMOREBI.
        Odpowiadaj po polsku, konkretnie i zwięźle: najwyżej 6 zdań albo krótka lista kroków (każdy w osobnej linii, z myślnikiem).
        Mów, co zrobić i jak sprawdzić, czy to pomogło. Gdy pytanie jest krótkim dopowiedzeniem, odnieś się do wcześniejszej rozmowy.
        Gdy sprawdzona porada pasuje do pytania, trzymaj się jej i nie dodawaj sprzecznych terminów ani liczb. Nie zmyślaj faktów, dawek ani nazw preparatów: przy nawozach i środkach odsyłaj do etykiety.
        Dane kolekcji wykorzystuj tylko wtedy, gdy pytanie dotyczy konkretnego drzewka albo stanu kolekcji. W dopowiedzeniach („i co dalej?”) zostań przy temacie poprzedniej wiadomości.
        Gdy brakuje szczegółu potrzebnego do odpowiedzi, zadaj jedno krótkie pytanie. Przy poważnych objawach zaznacz niepewność i zaproponuj konsultację ze specjalistą.
        Pytania niezwiązane z drzewkami i roślinami odrzuć jednym uprzejmym zdaniem.
        Pisz zwykłym tekstem, bez Markdown (bez gwiazdek, nagłówków i pogrubień).
        PROMPT;

    public function __construct(
        private readonly ClinicKnowledge $knowledge,
        private readonly OllamaClient $client,
    ) {
    }

    /**
     * @param list<array{role: string, text: string}> $history poprzednie wiadomości, najstarsza pierwsza
     * @param string $context opis kolekcji i ostatniego skanu użytkownika (może być pusty)
     * @return array{answer: string, matched: bool, topics: list<string>, source: string}
     */
    public function reply(string $question, array $history, string $context): array
    {
        $previousQuestion = '';
        foreach (array_reverse($history) as $message) {
            if ($message['role'] === 'user') {
                $previousQuestion = $message['text'];
                break;
            }
        }

        // Dopowiedzenie typu „i co zrobić?” nie ma słów kluczowych, więc porady szukamy też po poprzednim pytaniu.
        $advice = $this->knowledge->related($question . ' ' . $previousQuestion, 2);

        try {
            $answer = $this->clean($this->client->chat($this->messages($question, $history, $context, $advice), ['temperature' => 0.3, 'num_predict' => 450], 90));
            if ($answer !== '') {
                return ['answer' => $answer, 'matched' => true, 'topics' => [], 'source' => 'model'];
            }
        } catch (VisionException) {
            // Model niedostępny: poniżej odpowiada baza porad.
        }

        $fallback = $this->knowledge->answer($question);
        if (!$fallback['matched'] && $advice !== []) {
            $fallback = ['answer' => $advice[0], 'matched' => true, 'topics' => []];
        }

        return $fallback + ['source' => 'knowledge'];
    }

    /**
     * @param list<array{role: string, text: string}> $history
     * @param list<string> $advice
     * @return list<array<string, mixed>>
     */
    private function messages(string $question, array $history, string $context, array $advice): array
    {
        $system = self::SYSTEM_PROMPT;
        if ($advice !== []) {
            $system .= "\n\nSprawdzone porady:\n- " . implode("\n- ", $advice);
        }
        if ($context !== '') {
            $system .= "\n\nKolekcja użytkownika:\n" . $context;
        }

        $messages = [['role' => 'system', 'content' => $system]];
        foreach ($history as $message) {
            $messages[] = ['role' => $message['role'] === 'user' ? 'user' : 'assistant', 'content' => $message['text']];
        }
        $messages[] = ['role' => 'user', 'content' => $question];

        return $messages;
    }

    /** Model lubi Markdown: w oknie czatu zostawiamy zwykły tekst. */
    private function clean(string $text): string
    {
        $text = preg_replace(['/\*\*(.+?)\*\*/su', '/^#{1,6}\s*/mu', '/^\s*[*•]\s+/mu'], ['$1', '', '- '], $text) ?? $text;

        return trim(mb_substr($text, 0, 1500));
    }
}
