<?php

namespace StreetCsv;

use StreetCsv\Exception\UnRecognisedFormat;

class Parser
{
    public function __construct(private readonly Config $config)
    {
        
    }

    /**
     * @throws UnRecognisedFormat
     */
    public function parseEntry(string $entry): array
    {
        $entry = $this->fixWhitespace($entry);

        if($this->isOnePerson($entry)){
            return [ $this->parseOnePerson($entry) ];
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

        switch($partCount){
            case 3:
                $title = $parts[0];
                $maybeName = preg_replace('/[^A-Z0-9\-]/i', '', $parts[1]);
                if(strlen($maybeName) > 1){
                    $firstName = $maybeName;
                }else{
                    $initial = $maybeName;
                }
                $lastName = $parts[2];
                break;
            default:
                throw new UnRecognisedFormat("Could not determine format of name '$entry'");
        }

        return [
            'title' => $title ?? null,
            'first_name' => $firstName ?? null,
            'initial' => $initial ?? null,
            'last_name' => $lastName ?? null,
        ];
    }

    private function parsePeople(string $entry): array
    {
        return [];
    }

    private function matchPatterns($string, $patterns, &$matches = []) {
        $regex = '/^(' . implode('|', array_map(function($pattern) {
                return preg_quote($pattern, '/');
            }, $patterns)) . ')[ .]/';


        $matched =  preg_match_all($regex, $string, $matches, PREG_OFFSET_CAPTURE);

        return $matched;
    }

    private function fixWhitespace(string $entry): string
    {
        return preg_replace('/\s+/', ' ', trim($entry));
    }
}