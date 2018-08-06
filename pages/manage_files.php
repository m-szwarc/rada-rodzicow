<?php
if(!Session::$current_user->checkPriv(PRIV_ADD_QUESTION)) return -1;

if(!isset($_GET['question_id']) && !isset($_POST['question_id'])){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Nie został podany numer pytania.');
    echo('</div>');
    return;
}

$question_id = isset($_POST['question_id']) ? $_POST['question_id'] : $_GET['question_id'];
$question = null;
try{
    $question = new Question($question_id);
}catch(Exception $e){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Nie istnieje takie pytanie.');
    echo('</div>');
    return;
}

if($question->author->id != Session::$current_user->id){
    echo('<div class="card">');
    echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
    echo('  Możesz zarządzać plikami pytań utworzonych wyłącznie przez siebie.');
    echo('</div>');
    return;
}

if(isset($_POST['question_id'])){
    try{
        if(isset($_POST['use_file'])){
            $use_file = $_POST['use_file'];
            foreach($use_file as $id => $f){
                if($f == 0){
                    File::delete($id);
                }
            }
        }
        $files = reArrayFiles($_FILES['files']);
        File::upload($files, $question_id);

        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-check-circle green"></i> Pliki zostały zmienione</h1>');
        echo('  Wskazane pliki zostały zmodyfikowane.');
        echo('  <div class="card-buttons">');
        echo('    <a class="button flat colored right" href="view_question?question_id='.$question_id.'">Przejdź do pytania</a>');
        echo('    <a class="button flat right" href="question_list">Wróć do strony głównej</a>');
        echo('  </div>');
        echo('</div>');

        exit;
    }catch(Exception $e){
        echo('<div class="card">');
        echo('  <h1 class="card-header"><i class="fa fa-times-circle red"></i> Wystąpił błąd</h1>');
        echo($e->getMessage());
        echo('</div>');
    }
}

?>
<div class="card">
    <form action="manage_files" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="question_id" value="<?php echo($question_id); ?>" />
        <h1 class="card-header">Zarządzaj plikami</h1>
        <section>
            <h2>Pliki już przesłane</h2>
            <div class="already-uploaded-files">
                <?php echo(prepareCodeForFiles($question)); ?>
            </div>
        </section>
        <section>
            <h2>Prześlij nowe pliki</h2>
            <input type="file" class="inputfile" name="files[]" id="file-input" data-multiple-caption="Wybrano: {count}" multiple />
                <label for="file-input" class="flat">
                    <i class="fa fa-upload"></i>
                    Wybierz pliki (<span class="secondary">Nie wybrano</span>)
                </label>
        </section>
        <div class="card-buttons">
            <button type="submit" class="flat colored right">Zapisz</button>
        </div>
    </form>
</div>

<?php
function prepareCodeForFiles($question){
    $code = '';

    if(count($question->files) == 0) return '<i class="secondary">Nie przesłano plików.</i>';

    foreach($question->files as $file){
        $mime = getMimeTypeByFileName($file->getBaseName());
        $is_image = (strpos($mime, 'image/') !== false);


        $code.= '<div class="file-box" id="file-'.$file->id.'">';

        $code.= '<div class="image-container'.($is_image ? ' image' : '').'">';
        $code.= '<a href="files/'.$file->id.'" class="open-icon" title="Otwórz w nowej karcie" target="_blank">';
        $code.= '<i class="fa fa-external-link fa-fw"></i>';
        $code.= '</a>';
        if($is_image) $code.= '<img src="files/'.$file->id.'" />';
        $code.= '</div>';

        $code.= '<span class="file-name">'.$file->getBaseName().'</span>';
        $code.= '<a class="trash-icon" title="Usuń plik" onclick="disableFile('.$file->id.')"><i class="fa fa-trash"></i></a>';
        $code.= '</div>';
        $code.= '<input type="hidden" name="use_file['.$file->id.']" value="1" id="use_file_'.$file->id.'"/>';
    }

    return $code;
}
?>