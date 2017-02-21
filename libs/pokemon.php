<?php

/**
 * Pokemon Class.
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 * @version 0.1.0
 */
class Pokemon
{
    /**
     * Ladet das Config file für mit den benötigten parameter
     * @param string $file
     */
    public function __construct($path, $locale = "de") {
        $this->path = $path;
        $this->sticker = $this->path . "/stickers.json";
        $this->localeJson = $this->path . "/locales/" . $locale . "_pokemon.json";
        $this->movesJson = $this->path . "/locales/" . $locale . "_moves.json";
    }

    /**
     * Liest die Telegramsticker ID aus und gibt diese zurück
     * @param int $pokemonid
     * @return mixed
     */
    public function getSticker($pokemonid){
        $sticker = $this->stickerArray();
        return $sticker[$pokemonid];
    }


    /**
     * @param $movesid
     * @return mixed
     */
    public function getMoves($movesid){
        $array = $this->movesArray();
        return $array[$movesid];
    }

    /**
     * Sucht anhand der Pokemon ID den Namen
     * @param int $pokemonid
     * @return string
     */
    public function getName($pokemonid){
        $array = $this->localeArray();
        return $array[$pokemonid];
    }


    /**
     * Sucht anhand des Pokemon Namen die ID
     * @param string $pokemonname
     * @return int
     */
    public function getID($pokemonname){
        foreach($this->localeArray() as $id => $name){
            if(strtolower($name) == trim(strtolower($pokemonname))){
                return $id;
            }
        }
    }


    /**
     * @param $pokemonid
     * @return mixed
     */
    protected function getLocale($pokemonid){
        $pokemon = $this->localeArray();
        return $pokemon[$pokemonid];

    }

    /**
     * @return mixed
     */
    protected function stickerArray(){
        return $this->getJson($this->sticker);
    }

    /**
     * @return mixed
     */
    protected function localeArray(){
        return $this->getJson($this->localeJson);
    }

    /**
     * @return mixed
     */
    protected function movesArray(){
        return $this->getJson($this->movesJson);
    }


    /**
     * @param $file
     * @return mixed
     */
    protected function getJson($file){
        return json_decode(file_get_contents($file), TRUE);
    }


}