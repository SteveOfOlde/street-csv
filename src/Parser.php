<?php

namespace StreetCsv;

class Parser
{
    public function __construct(private readonly Config $config)
    {
        
    }

    public function parseEntry(string $entry): array
    {
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

    private function parseOnePerson(string $entry)
    {
        $titles = [];
        $this->matchPatterns($entry, $this->config->titles, $titles);
        $titles = array_unique($titles);

        echo '$titles : ';
        print_r($titles);
        ob_flush();
        exit;

        return [
            'title' => null,
            'first_name' => null,
            'initial' => null,
            'last_name' => null,
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
}