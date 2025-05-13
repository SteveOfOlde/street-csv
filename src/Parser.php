<?php

namespace StreetCsv;

use StreetCsv\Exception\UnRecognisedFormat;

readonly class Parser
{
    private string $titlesPattern;

    public function __construct(private Config $config)
    {
        $this->titlesPattern = '\b' . join('\b|\b', $this->config->titles) . '\b';
    }

    /**
     * @throws UnRecognisedFormat
     */
    public function parseEntry(string $entry): array
    {
        $entry = $this->fixWhitespace($entry);
        $entry = $this->fixCharacters($entry);

        if ($this->isOnePerson($entry)) {
            return [$this->parseOnePerson($entry)];
        }

        return $this->parsePeople($entry);
    }

    /**
     * @param string $haystack
     * @param string[] $needles
     * @return bool
     */
    private function contains(string $haystack, array $needles): bool
    {
        return array_any($needles, fn($a) => stripos($haystack, $a) !== false);
    }

    private function isOnePerson(string $entry): bool
    {
        return !$this->contains($entry, $this->config->conjunctions);
    }

    /**
     * @throws UnRecognisedFormat
     */
    private function parseOnePerson(string $entry): array
    {
        $parts = explode(' ', $entry);
        $partCount = count($parts);

        switch ($partCount) {
            case 3:
                $title = $parts[0];
                $maybeName = $parts[1];
                if (strlen($maybeName) > 1) {
                    $firstName = $maybeName;
                } else {
                    $initial = $maybeName;
                }
                $lastName = $parts[2];
                break;
            default:
                throw new UnRecognisedFormat("Could not determine single person format of name '$entry'");
        }

        return [
            'title' => $title,
            'first_name' => $firstName ?? null,
            'initial' => $initial ?? null,
            'last_name' => $lastName,
        ];
    }

    /**
     * @throws UnRecognisedFormat
     */
    private function parsePeople(string $entry): array
    {
        $entry = str_ireplace($this->config->conjunctions, '', $entry);
        /** @var list<string> $parts */
        $parts = preg_split("/($this->titlesPattern)/", $entry, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $parts = array_filter(array_map(fn($s) => trim($s), $parts));

        $partCount = count($parts);

        switch ($partCount) {
            case 3:
                $lastName = array_pop($parts);

                $people = [];
                foreach ($parts as $title) {
                    if (!in_array($title, $this->config->titles)) {
                        continue;
                    }
                    $people[] = [
                        'title' => $title,
                        'first_name' => null,
                        'initial' => null,
                        'last_name' => $lastName,
                    ];
                }

                return $people;
            case 4:

                [$first, $last] = explode(' ', $parts[1]);

                if (strlen($first) > 1) {
                    $firstname = $first;
                    $initial = null;
                } else {
                    $firstname = null;
                    $initial = $first;
                }

                $person1 = [
                    'title' => $parts[0] ?? null,
                    'first_name' => $firstname ?? null,
                    'initial' => $initial ?? null,
                    'last_name' => $last,
                ];

                [$first, $last] = explode(' ', $parts[3]);

                if (strlen($first) > 1) {
                    $firstname = $first;
                    $initial = null;
                } else {
                    $firstname = null;
                    $initial = $first;
                }

                $person2 = [
                    'title' => $parts[2] ?? null,
                    'first_name' => $firstname ?? null,
                    'initial' => $initial ?? null,
                    'last_name' => $last,
                ];

                return [$person1, $person2];
            default:
                throw new UnRecognisedFormat("Could not determine people format of name '$entry'");
        }
    }

    private function fixWhitespace(string $entry): string
    {
        return preg_replace('/\s+/', ' ', trim($entry)) ?? '';
    }

    private function fixCharacters(string $entry): string
    {
        return preg_replace('/[^\p{L}\s&-]/ui', '', $entry) ?? '';
    }
}