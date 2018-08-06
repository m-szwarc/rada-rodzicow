<?php
$user_first_name = Session::$current_user->first_name;
$last_char = mb_substr($user_first_name, -1);
$is_female = ($last_char == 'a');

function f($male = 'eś', $female = 'aś'){
    global $is_female;
    if($is_female) echo($female);
    else echo($male);
}
?>
<div class="card">
    <h1 class="card-header">Pomoc</h1>

    <section>
        <h2>Spis treści</h2>
        <ul>
            <li><a href="#question_list">Lista pytań</a></li>
            <li><a href="#voting">Oddawanie głosów</a></li>
            <li><a href="#archive">Archiwum pytań</a></li>
        </ul>
    </section>

    <section id="question_list" class="justify">
        <h2>Lista pytań</h2>

        Na stronie głównej znajdują się głosowania obecnej kadencji Rady Rodziców, podzielone na trzy grupy:
        <ul>
            <li><em>Aktualne</em> &ndash; tu znajdują się pytania, na które jeszcze nie odpowiedział<?php f(); ?>.</li>
            <li><em>Nieaktywne</em> &ndash; do tej grupy trafiają te pytania, na które już zagłosował<?php f(); ?>, ale jeszcze nie upłynął limit czasu.</li>
            <li><em>Zakończone</em> &ndash; tutaj znajdują się głosowania, dla których już upłynął limit czasu na oddanie głosu.</li>
        </ul>
    </section>

    <section id="voting" class="justify">
        <h2>Oddawanie głosów</h2>

        Na każde pytanie można oddać głos z dwóch miejsc:
        <ul>
            <li>Strony głównej</li>
            <li>Strony ze szczegółami głosowania</li>
        </ul>
        <p>
            Pod treścią pytania w obu miejscach znajduje się informacja o limicie czasu, a pod nią umiejscowione są
            przyciski do głosowania. Jeżeli do pytania zostały dołączone pliki,
            będą one widoczne tylko na stronie ze szczegółami.
            O ich obecności informuje ikonka <i class="fa fa-link"></i> przy treści głosowania na stronie głównej.
        </p>

        <em>Nie możesz zmienić swojego głosu. Po wybraniu jednej opcji, nie będzie możliwe zaznaczenie innej.</em>

        <div class="card flat-card">
            <big class="card-header no-border justify">Tu znajduje się treść pytania</big>
            <span class="secondary"><i class="fa fa-clock-o fa-fw"></i> Informacja o limicie czasu</span>
            <div class="card-buttons">
                <button class="flat colored" type="button" title="Liczba w nawiasie oznacza ilość głosów">Odpowiedź A (0)</button>
                <button class="flat colored" type="button" title="Liczba w nawiasie oznacza ilość głosów">Odpowiedź B (0)</button>
                <button class="flat colored" type="button" title="Liczba w nawiasie oznacza ilość głosów">Odpowiedź C (0)</button>
                
                <button type="button" class="flat right" title="Ten link prowadzi do strony ze szczegółami">Szczegóły</button>
            </div>
        </div>
    </section>

    <section id="archive" class="justify">
        <h2>Archiwum pytań</h2>

        Na koniec każdej kadencji (tj. 30 września każdego roku) pytania są przenoszone do archiwum.
        Na dole strony głównej wyświetlane są linki do tylko tych kadencji,
        podczas których był<?php f(); ?> członkiem Rady Rodziców.
    </section>
</div>