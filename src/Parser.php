<?php

namespace StreetCsv;

use StreetCsv\Exception\UnRecognisedFormat;

class Parser
{
    private readonly string $titlesPattern;

    public function __construct(private readonly Config $config)
    {
        $this->titlesPattern = '\b'.join('\b|\b', $this->config->titles).'\b';
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

    private function contains($haystack, array $needles): bool
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
            'title' => $title ?? null,
            'first_name' => $firstName ?? null,
            'initial' => $initial ?? null,
            'last_name' => $lastName ?? null,
        ];
    }

    /**
     * @throws UnRecognisedFormat
     */
    private function parsePeople(string $entry): array
    {
        $entry = str_ireplace($this->config->conjunctions, '', $entry);
//        echo '$entry : ',$entry,"\n";
//        echo '$this->titlesPattern : ',$this->titlesPattern,"\n";
        $parts = preg_split("/($this->titlesPattern)/", $entry, -1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $parts = array_filter(array_map(fn($s)=> trim($s), $parts));

        $partCount = count($parts);

        $lastName = array_pop($parts);
//        echo '$partCount : ', $partCount , "\n";
//        echo '$lastName : ', $lastName , "\n";
//        print_r($parts);
//        ob_flush();

        switch ($partCount) {
            case 3:
                $people = [];
                foreach($this->config->titles as $title) {
//                    echo '$title : ', $title, "\n";
                    if(!in_array($title, $parts)) {
                        continue;
                    }
                    $count = count(array_filter($parts, fn($s)=> strtolower($s) === strtolower($title)));
//                    echo '$count : ', $count, "\n";
                    for ($i = 0; $i < $count; $i++) {
                        $people[] = [
                            'title' => $title ?? null,
                            'first_name' => null,
                            'initial' => null,
                            'last_name' => $lastName,
                        ];
                    }
                }

                return $people;
            default:
                throw new UnRecognisedFormat("Could not determine people format of name '$entry'");
        }
    }

    private function matchPatterns($string, $patterns, &$matches = [])
    {
        $regex = '/^(' . implode('|', array_map(function ($pattern) {
                return preg_quote($pattern, '/');
            }, $patterns)) . ')[ .]/';


        $matched = preg_match_all($regex, $string, $matches, PREG_OFFSET_CAPTURE);

        return $matched;
    }

    private function fixWhitespace(string $entry): string
    {
        return preg_replace('/\s+/', ' ', trim($entry));
    }

    private function fixCharacters(string $entry): string
    {
        return preg_replace('/[^\p{L}\s]/ui', '', $entry);
    }
}