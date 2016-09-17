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
        $this->pokemonJson = $this->path . "/pokemon.json";
        $this->localeJson = $this->path . "/locales/" . $locale . "_pokemon.json";
        $this->movesJson = $this->path . "/locales/" . $locale . "_moves.json";
    }

    /**
     * Liest die Telegramsticker ID aus und gibt diese zurück
     * @param int $pokemonid
     * @return mixed
     */
    public function getSticker($pokemonid){
        return $this->getPokemon($pokemonid, "Sticker");
    }

    /**
     * Liest die Rarität angabe aus und gibt diese zurück.
     * @param int $pokemonid
     * @return string
     */
    public function getNotify($pokemonid){
            return $this->getPokemon($pokemonid, "Notify");

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
     * Sucht anhand der Pokemon ID den Namen
     * @param int $pokemonid
     * @return string
     */
    public function getRarity($pokemonid){
        switch( $this->getPokemon($pokemonid, "Rarity") ){
            case "Common":
                return "Häufig";
                break;
            case "Uncommon":
                return "Nicht Häufig";
                break;
            case "Rare":
                return "Selten";
                break;
            case "Very Rare":
                return "Sehr Selten";
                break;
            case "Ultra Rare":
                return "Ultra Selten";
                break;
        }
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
     * Bereitet das Pokemon Array für den zugriff vor
     * @param int $pokemonid
     * @param string $info
     * @return array
     */
    protected function getPokemon($pokemonid, $info){
        $pokemon = $this->pokemonArray();
        return $pokemon[$pokemonid][$info];

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
     * @return array
     */
    public function pokemonArray(){
        return $this->getJson($this->pokemonJson);
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