<?php
class File{
    public $id;
    public $alt_id;
    public $real_path;
    public $question_id;

    public function __construct($id){
        $condition = 'id='.$id.' OR alt_id="'.$id.'"';
        if(!is_numeric($id)) $condition = 'alt_id="'.$id.'"';
        $files = DB::query('SELECT * FROM '.TABLE_FILES.' WHERE '.$condition);
        if($files->num_rows == 0) throw new RRException("File with id $id wasn't found.");

        $files = $files->fetch_assoc();

        $this->id = $files['id'];
        $this->alt_id = $files['alt_id'];
        $this->real_path = $files['path'];
        $this->question_id = $files['related_question'];
    }

    public function getBaseName(){
        $name = $this->real_path;
        if(strrpos($name, '+') != false){
            $name = substr($name, 0, strrpos($name, '+'));
        }
        return basename($name);
    }
    
    public static function upload($array, $qid){
        $target_dir = 'uploads/';
        foreach($array as $file){
            if($file['error'] == 4) continue;
            $target_file = $target_dir.basename($file['name']).'+'.time();
            if(move_uploaded_file($file['tmp_name'], $target_file)){
                if(!DB::query('INSERT INTO '.TABLE_FILES." (id, path, related_question) VALUES (NULL, '$target_file', $qid)"))
                    throw new RRException(DB::getError());
            }else{
                throw new RRException('Nie można przesłać pliku '.$file['name'].'. Błąd: '.self::getErrorText($file['error']));
            }
        }
    }

    private static function getErrorText($num){
        switch($num){
            case 1: return 'Plik jest zbyt duży';
            case 2: return 'Plik przekracza limit wielkości';
            case 3: return 'Plik został częściowo przesłany';
            case 4: return 'Nie przesłano pliku';
            case 6: return 'Brakuje folderu tymczasowego';
            case 7: return 'Nie udało się zapisać pliku';
            case 8: return 'Rozszerzenie PHP zatrzymało wysyłanie pliku';
        }
        return $num;
    }

    public static function delete($id){
        if(!DB::query('DELETE FROM '.TABLE_FILES.' WHERE id='.$id)) throw new RRException(DB::getError());
    }
}
?>