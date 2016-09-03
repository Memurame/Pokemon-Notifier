<?php

/**
 * Pokemon Class.
 * @author Thomas Hirter <t.hirter@outlook.com>
 */
class Pokemon
{
    public function __construct($file) {
        $this->file = $file;
    }

    public function getSticker($pokemonid){
        return $this->getPokemon($pokemonid, "Sticker");
    }

    public function getNotify($pokemonid){
            return $this->getPokemon($pokemonid, "Notify");

    }

    public function getName($pokemonid){
        return $this->getPokemon($pokemonid, "Name");
    }

    public function getID($pokemonname){
        foreach($this->pokemonArray() as $id => $value){
            if(strtolower($value['Name']) == trim(strtolower($pokemonname))){
                return $id;
            }
        }
    }

    protected function getPokemon($pokemonid, $info){
        $pokemon = $this->pokemonArray();
        return $pokemon[$pokemonid][$info];

    }

    public function pokemonArray(){
        return json_decode(file_get_contents($this->file), TRUE);
    }

}