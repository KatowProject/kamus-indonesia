<?php
defined("BASEPATH") or exit("No direct script access allowed");

require_once '../scraper.php';
require_once '../database/database.php';

class Controller extends Database
{
    public $scraper;
    public function __construct()
    {
        parent::__construct();

        $this->scraper = new Scraper();
    }

    public $type = [
        "is" => 2,
        "ish" => 3,
        "isk" => 4,
        "i" => 1
    ];

    public $type_h = [
        "is" => "Sunda Sedang",
        "ish" => "Sunda Halus",
        "isk" => "Sunda Kasar",
        "i" => "Indonesia"
    ];

    public function get_word($word, $type)
    {
        $_type = $this->type[$type];
        $result = $this->get(strtolower($word), $_type);

        if ($result):
            return $result;
        else:
            $result = $this->scraper->get_data($word, $type);
            if ($result):
                $this->insert(
                    array(
                        'word' => $word,
                        'translated' => $result['word'],
                        'type' => $result['type']
                    )
                );

                $result['lang'] = $this->type_h[$type];
                return $result;
            else:
                return [
                    'word' => $word,
                    'translated' => $word,
                    'lang' => 'Native'
                ];
            endif;
        endif;
    }

    public function get_word_i($word)
    {
        $_type = $this->type['is'];

        $result = $this->get(strtolower($word), $_type);
        if ($result):
            $result['lang'] = $this->type_h['i'];

            return $result;
        else:
            $result = $this->scraper->get_data($word, 'sih');
            if ($result):
                $result['lang'] = $this->type_h['i'];
                $this->insert(
                    array(
                        'word' => $result['translated'],
                        'translated' => $word,
                        'type' => $this->type['is']
                    )
                );
                return $result;
            else:
                return [
                    'word' => $word,
                    'translated' => $word,
                    'lang' => 'Native'
                ];
            endif;
        endif;

    }

    static function splitRemoveSpecialChars($word)
    {
        $words = preg_split('/\s+/', $word);
        $words = array_map(function ($word) {
            return preg_replace('/[^A-Za-z0-9\-]/', '', $word);
        }, $words);

        return $words;
    }
}